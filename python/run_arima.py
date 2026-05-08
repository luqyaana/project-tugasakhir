import argparse
import json
import math
import sys
import warnings
from pathlib import Path

try:
    import numpy as np
    import pandas as pd
    from statsmodels.tsa.arima.model import ARIMA
    from statsmodels.tsa.statespace.sarimax import SARIMAX
    from statsmodels.tsa.stattools import adfuller
except ImportError as exc:
    print(
        json.dumps(
            {
                "status": "error",
                "message": (
                    "Library Python untuk ARIMA belum lengkap. "
                    "Pastikan numpy, pandas, dan statsmodels sudah terpasang."
                ),
                "detail": str(exc),
            }
        )
    )
    sys.exit(1)


MAX_P = 5
MAX_Q = 5
TOP_CANDIDATES_LIMIT = 5
MIN_ROWS = 24
RESAMPLE_FREQUENCY = "1h"
SHORT_GAP_LIMIT = 3


def safe_round(value, digits=4):
    if value is None:
        return None

    value = float(value)

    if math.isnan(value) or math.isinf(value):
        return None

    return round(value, digits)


def read_csv_flexible(file_path: Path) -> pd.DataFrame:
    encodings = ["utf-8", "utf-8-sig", "latin1", "cp1252", "ISO-8859-1"]
    separators = [None, ";", ",", "\t"]
    last_error = None

    for encoding in encodings:
        for separator in separators:
            try:
                frame = pd.read_csv(
                    file_path,
                    sep=separator,
                    engine="python",
                    encoding=encoding,
                )

                if frame.shape[1] > 0:
                    return frame
            except Exception as exc:
                last_error = exc

    raise ValueError(f"Gagal membaca file {file_path.name}: {last_error}")


def normalize_columns(frame: pd.DataFrame) -> pd.DataFrame:
    frame = frame.copy()
    frame.columns = [str(column).strip().lower() for column in frame.columns]

    rename_map = {}

    if "datetime" in frame.columns:
        rename_map["datetime"] = "timestamp"
    elif "tanggal" in frame.columns:
        rename_map["tanggal"] = "timestamp"
    elif "waktu_pencatatan" in frame.columns:
        rename_map["waktu_pencatatan"] = "timestamp"

    if "nilai_no2" in frame.columns:
        rename_map["nilai_no2"] = "no2"

    frame = frame.rename(columns=rename_map)

    if "timestamp" not in frame.columns and len(frame.columns) >= 1:
        frame = frame.rename(columns={frame.columns[0]: "timestamp"})

    if "no2" not in frame.columns and len(frame.columns) >= 2:
        frame = frame.rename(columns={frame.columns[1]: "no2"})

    required_columns = {"timestamp", "no2"}
    if not required_columns.issubset(set(frame.columns)):
        raise ValueError("File harus memiliki kolom datetime/timestamp dan NO2.")

    return frame[["timestamp", "no2"]].copy()


def series_to_rows(series: pd.Series):
    series = series.dropna()

    if series.empty:
        return []

    return [
        {
            "label": index.strftime("%Y-%m-%d %H:%M:%S"),
            "value": safe_round(value, 4),
        }
        for index, value in series.items()
    ]


def sample_series(series: pd.Series, max_points=120):
    series = series.dropna()

    if series.empty:
        return []

    if len(series) <= max_points:
        return series_to_rows(series)

    base_indexes = np.linspace(0, len(series) - 1, num=max_points, dtype=int)
    extreme_indexes = set(series.nlargest(min(10, len(series))).index).union(
        set(series.nsmallest(min(10, len(series))).index)
    )
    indexed_positions = {index: position for position, index in enumerate(series.index)}
    selected_positions = {int(position) for position in base_indexes}

    for index in extreme_indexes:
        selected_positions.add(indexed_positions[index])

    sampled = series.iloc[sorted(selected_positions)]

    return series_to_rows(sampled)


def build_gap_masks(series: pd.Series):
    is_missing = series.isna()
    group_id = is_missing.ne(is_missing.shift()).cumsum()
    group_sizes = is_missing.groupby(group_id).transform("sum")
    short_gap_mask = is_missing & (group_sizes <= SHORT_GAP_LIMIT)
    long_gap_mask = is_missing & (group_sizes > SHORT_GAP_LIMIT)

    return short_gap_mask, long_gap_mask


def preprocess_dataset(file_path: Path):
    frame = read_csv_flexible(file_path)
    dataset = normalize_columns(frame)

    dataset["timestamp"] = pd.to_datetime(
        dataset["timestamp"],
        errors="coerce",
    )

    dataset["no2"] = (
        dataset["no2"]
        .astype(str)
        .str.replace(",", ".", regex=False)
        .str.replace(" ", "", regex=False)
    )
    dataset["no2"] = dataset["no2"].replace(["", "-", "nan", "None", "NULL", "NaN"], np.nan)
    dataset["no2"] = pd.to_numeric(dataset["no2"], errors="coerce")
    dataset = dataset.dropna(subset=["timestamp"]).sort_values("timestamp")

    if dataset.empty:
        raise ValueError("Tidak ada timestamp valid yang dapat diproses.")

    dataset = dataset.groupby("timestamp", as_index=False)["no2"].mean()
    raw_series = dataset.set_index("timestamp")["no2"].sort_index()

    hourly = raw_series.resample(RESAMPLE_FREQUENCY).mean().to_frame(name="no2")
    missing_after_resample = int(hourly["no2"].isna().sum())
    zero_or_negative_count = int((hourly["no2"] <= 0).fillna(False).sum())

    hourly.loc[hourly["no2"] <= 0, "no2"] = np.nan
    hourly["no2_before_imputation"] = hourly["no2"].copy()

    short_gap_mask, long_gap_mask = build_gap_masks(hourly["no2"])
    interpolated = hourly["no2"].interpolate(
        method="time",
        limit=SHORT_GAP_LIMIT,
        limit_direction="both",
    )

    hourly["no2_imputed"] = hourly["no2"].copy()
    hourly.loc[short_gap_mask, "no2_imputed"] = interpolated.loc[short_gap_mask]

    hourly_median = hourly.groupby(hourly.index.hour)["no2"].median()
    global_median = hourly["no2"].median()

    for index in hourly.index[long_gap_mask]:
        hour = index.hour
        replacement = hourly_median.loc[hour] if hour in hourly_median.index else np.nan
        hourly.loc[index, "no2_imputed"] = replacement if not pd.isna(replacement) else global_median

    hourly["no2_imputed"] = hourly["no2_imputed"].fillna(global_median)

    cleaned_series = hourly["no2_imputed"].copy()
    q1 = cleaned_series.quantile(0.25)
    q3 = cleaned_series.quantile(0.75)
    iqr = q3 - q1
    lower_bound = q1 - 1.5 * iqr
    low_outlier_mask = cleaned_series < lower_bound

    cleaned_series.loc[low_outlier_mask] = np.nan
    hourly_median_after = cleaned_series.groupby(cleaned_series.index.hour).median()
    global_median_after = cleaned_series.median()

    for index in cleaned_series.index[low_outlier_mask]:
        hour = index.hour
        replacement = hourly_median_after.loc[hour] if hour in hourly_median_after.index else np.nan
        cleaned_series.loc[index] = replacement if not pd.isna(replacement) else global_median_after

    cleaned_series = cleaned_series.fillna(global_median_after).astype(float)

    if len(cleaned_series) < MIN_ROWS:
        raise ValueError(
            f"Dataset minimal harus memiliki {MIN_ROWS} data hasil preprocessing untuk ARIMA."
        )

    preprocessing = {
        "time_column": "timestamp",
        "component_column": None,
        "value_column": "no2",
        "row_count_raw": int(len(frame)),
        "row_count_grouped": int(len(raw_series)),
        "row_count_hourly": int(len(cleaned_series)),
        "missing_before": int(missing_after_resample + zero_or_negative_count),
        "missing_after": int(cleaned_series.isna().sum()),
        "missing_after_resample": missing_after_resample,
        "zero_or_negative_count": zero_or_negative_count,
        "short_gap_count": int(short_gap_mask.sum()),
        "long_gap_count": int(long_gap_mask.sum()),
        "low_outlier_count": int(low_outlier_mask.sum()),
        "interval_label": "1 jam",
        "raw_preview": series_to_rows(hourly["no2_before_imputation"]),
        "clean_preview": series_to_rows(cleaned_series),
    }

    return cleaned_series.asfreq(RESAMPLE_FREQUENCY), preprocessing


def run_adf_test(series: pd.Series):
    try:
        statistic, p_value, _, _, critical_values, _ = adfuller(series.dropna(), autolag="AIC")
        return {
            "adf_stat": safe_round(statistic),
            "p_value": safe_round(p_value),
            "critical_values": {
                key: safe_round(value) for key, value in critical_values.items()
            },
        }
    except Exception:
        return {
            "adf_stat": None,
            "p_value": None,
            "critical_values": {},
        }


def determine_differencing(series: pd.Series):
    adf_before = run_adf_test(series)
    p_value = adf_before.get("p_value")

    if p_value is not None and p_value < 0.05:
        return 0, series.copy(), adf_before, adf_before

    diff1 = series.diff().dropna()
    adf_after = run_adf_test(diff1)

    if adf_after.get("p_value") is not None and adf_after["p_value"] < 0.05:
        return 1, diff1, adf_before, adf_after

    diff2 = series.diff(2).dropna()
    adf_after = run_adf_test(diff2)
    return 2, diff2, adf_before, adf_after


def split_train_test(series: pd.Series):
    train_size = int(len(series) * 0.8)
    train = series.iloc[:train_size]
    test = series.iloc[train_size:]

    if len(train) < 12 or len(test) < 6:
        raise ValueError("Data train/test tidak cukup untuk evaluasi ARIMA.")

    return train, test


def evaluate_forecast(actual, predicted):
    actual_values = np.asarray(actual, dtype=float)
    predicted_values = np.asarray(predicted, dtype=float)
    errors = actual_values - predicted_values
    abs_errors = np.abs(errors)
    squared_errors = np.square(errors)
    mse = float(np.mean(squared_errors))
    actual_mean = float(np.mean(actual_values))
    total_sum_squares = float(np.sum(np.square(actual_values - actual_mean)))
    residual_sum_squares = float(np.sum(squared_errors))
    r2 = None

    if total_sum_squares > 0:
        r2 = 1 - (residual_sum_squares / total_sum_squares)

    return {
        "mae": safe_round(np.mean(abs_errors)),
        "mse": safe_round(mse),
        "rmse": safe_round(np.sqrt(mse)),
        "r2": safe_round(r2),
    }


def find_best_arima_order(train: pd.Series, d: int):
    candidates = []
    best_candidate = None

    with warnings.catch_warnings():
        warnings.simplefilter("ignore")

        for p in range(0, MAX_P + 1):
            for q in range(0, MAX_Q + 1):
                if p == 0 and d == 0 and q == 0:
                    continue

                try:
                    fitted = ARIMA(
                    train,
                    order=(p, d, q),
                ).fit()

                    candidate = {
                        "order": {"p": p, "d": d, "q": q},
                        "aic": safe_round(fitted.aic),
                        "bic": safe_round(fitted.bic),
                    }
                    candidates.append(candidate)

                    current_aic = float(fitted.aic)
                    if best_candidate is None or current_aic < best_candidate["aic"]:
                        best_candidate = {
                            "order": (p, d, q),
                            "aic": current_aic,
                            "bic": float(fitted.bic),
                        }
                except Exception:
                    continue

    if best_candidate is None:
        raise ValueError("Tidak ada kombinasi parameter ARIMA yang berhasil difit.")

    candidates.sort(
        key=lambda item: (
            float("inf") if item["aic"] is None else item["aic"],
            float("inf") if item["bic"] is None else item["bic"],
        )
    )

    return {
        "best_order": best_candidate["order"],
        "aic": safe_round(best_candidate["aic"]),
        "bic": safe_round(best_candidate["bic"]),
        "top_candidates": candidates[:TOP_CANDIDATES_LIMIT],
        "candidate_count": len(candidates),
    }


def evaluate_standard_arima(train: pd.Series, test: pd.Series, order):
    with warnings.catch_warnings():
        warnings.simplefilter("ignore")
        fitted = ARIMA(
        train,
        order=order,
        enforce_stationarity=False,
        enforce_invertibility=False,
    ).fit()

    forecast_result = fitted.get_forecast(steps=len(test))
    forecast = forecast_result.predicted_mean
    forecast.index = test.index
    metrics = evaluate_forecast(test.values, forecast.values)

    return {
        "metrics": metrics,
        "forecast": forecast,
    }


def evaluate_rolling_arima(train: pd.Series, test: pd.Series, order):
    train_series = train.copy().asfreq(RESAMPLE_FREQUENCY)
    test_series = test.copy().asfreq(RESAMPLE_FREQUENCY)
    train_series.name = "NO2"
    test_series.name = "NO2"

    with warnings.catch_warnings():
        warnings.simplefilter("ignore")
        rolling_model = SARIMAX(
            train_series,
            order=order,
            seasonal_order=(0, 0, 0, 0),
            enforce_stationarity=False,
            enforce_invertibility=False,
        ).fit(disp=False)

    predictions = []

    for index in range(len(test_series)):
        yhat = rolling_model.forecast(steps=1).iloc[0]
        predictions.append(float(yhat))

        new_observation = pd.Series(
            [test_series.iloc[index]],
            index=[test_series.index[index]],
            name="NO2",
        )
        rolling_model = rolling_model.append(new_observation, refit=False)

    forecast = pd.Series(predictions, index=test_series.index, name="forecast")
    metrics = evaluate_forecast(test_series.values, forecast.values)

    return {
        "metrics": metrics,
        "forecast": forecast,
        "actual": test_series,
    }


def fit_best_arima(series: pd.Series, d: int):
    train, test = split_train_test(series)
    order_search = find_best_arima_order(train, d)
    best_order = order_search["best_order"]
    evaluated_candidates = []

    for candidate in order_search["top_candidates"]:
        try:
            candidate_order = (
                candidate["order"]["p"],
                candidate["order"]["d"],
                candidate["order"]["q"],
            )
            rolling_metrics = evaluate_rolling_arima(train, test, candidate_order)["metrics"]

            evaluated_candidates.append(
                {
                    **candidate,
                    "mae": rolling_metrics["mae"],
                    "mse": rolling_metrics["mse"],
                    "rmse": rolling_metrics["rmse"],
                    "r2": rolling_metrics["r2"],
                }
            )
        except Exception:
            evaluated_candidates.append(candidate)

    standard_result = evaluate_standard_arima(train, test, best_order)
    rolling_result = evaluate_rolling_arima(train, test, best_order)

    return {
        "train": train,
        "test": test,
        "best_order": best_order,
        "aic": order_search["aic"],
        "bic": order_search["bic"],
        "top_candidates": evaluated_candidates,
        "candidate_count": order_search["candidate_count"],
        "standard": standard_result,
        "rolling": rolling_result,
    }


def build_future_forecast(series: pd.Series, best_order, forecast_steps: int):
    season_window = series.tail(min(len(series), 14 * 24))
    hourly_mean = season_window.groupby(season_window.index.hour).mean()
    overall_mean = season_window.mean()
    hourly_effect = hourly_mean - overall_mean

    seasonal_component = pd.Series(
        [hourly_effect.loc[index.hour] if index.hour in hourly_effect.index else 0.0 for index in series.index],
        index=series.index,
    )
    adjusted_series = series - seasonal_component

    with warnings.catch_warnings():
        warnings.simplefilter("ignore")
        fitted = SARIMAX(
            adjusted_series,
            order=best_order,
            seasonal_order=(0, 0, 0, 0),
            enforce_stationarity=False,
            enforce_invertibility=False,
        ).fit(disp=False)

    future_index = pd.date_range(
        start=series.index[-1] + pd.Timedelta(hours=1),
        periods=forecast_steps,
        freq=RESAMPLE_FREQUENCY,
    )
    adjusted_result = fitted.get_forecast(steps=forecast_steps)
    adjusted_mean = adjusted_result.predicted_mean
    adjusted_ci = adjusted_result.conf_int()

    future_seasonal = pd.Series(
        [hourly_effect.loc[index.hour] if index.hour in hourly_effect.index else 0.0 for index in future_index],
        index=future_index,
    )
    final_forecast = adjusted_mean.values + future_seasonal.values
    lower_ci = adjusted_ci.iloc[:, 0].values + future_seasonal.values
    upper_ci = adjusted_ci.iloc[:, 1].values + future_seasonal.values

    forecast_rows = []
    for index, value, lower, upper in zip(future_index, final_forecast, lower_ci, upper_ci):
        forecast_rows.append(
            {
                "datetime": index.strftime("%Y-%m-%d %H:%M:%S"),
                "value": safe_round(value, 2),
                "lower_ci": safe_round(lower, 2),
                "upper_ci": safe_round(upper, 2),
            }
        )

    return forecast_rows


def build_summary(best_order, candidate_count: int, total_rows: int, metrics: dict, forecast_steps: int):
    p, d, q = best_order
    return (
        f"ARIMA terbaik untuk dataset ini adalah ARIMA({p},{d},{q}) "
        f"setelah mengevaluasi {candidate_count} kandidat dari {total_rows} data per jam. "
        f"Evaluasi utama menggunakan Rolling ARIMA menghasilkan MAE {metrics['mae']}, "
        f"MSE {metrics['mse']}, RMSE {metrics['rmse']}, dan R2 {metrics['r2']}. "
        f"Forecast berikutnya dibuat sebanyak {forecast_steps} jam dengan Seasonal Adjusted ARIMA."
    )


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--file", required=True)
    parser.add_argument("--dataset-id", required=True)
    parser.add_argument("--forecast-steps", type=int, default=24)
    args = parser.parse_args()

    try:
        file_path = Path(args.file)
        series, preprocessing = preprocess_dataset(file_path)
        d, stationary_series, adf_before, adf_after = determine_differencing(series)
        model_result = fit_best_arima(series, d)
        best_order = model_result["best_order"]
        forecast = build_future_forecast(series, best_order, args.forecast_steps)
        standard_metrics = model_result["standard"]["metrics"]
        rolling_metrics = model_result["rolling"]["metrics"]
        rolling_actual = model_result["rolling"]["actual"]
        rolling_forecast = model_result["rolling"]["forecast"]

        payload = {
            "status": "success",
            "dataset_id": int(args.dataset_id),
            "model_name": "ARIMA Auto Grid Search",
            "parameters": {
                "p": best_order[0],
                "d": best_order[1],
                "q": best_order[2],
            },
            "uji": {
                "observasi": int(len(series)),
                "nilai_rata_rata": safe_round(series.mean(), 2),
                "aic": model_result["aic"],
                "bic": model_result["bic"],
                "mae": rolling_metrics["mae"],
                "mse": rolling_metrics["mse"],
                "rmse": rolling_metrics["rmse"],
                "r2": rolling_metrics["r2"],
                "candidate_count": model_result["candidate_count"],
                "adf_before": adf_before,
                "adf_after": adf_after,
                "top_candidates": model_result["top_candidates"],
                "test_actual": [safe_round(value, 2) for value in rolling_actual.tolist()],
                "test_forecast": [safe_round(value, 2) for value in rolling_forecast.tolist()],
                "test_labels": [
                    index.strftime("%Y-%m-%d %H:%M:%S") for index in rolling_actual.index
                ],
                "preprocessing": preprocessing,
                "interval_label": preprocessing["interval_label"],
                "differencing_order": d,
                "differencing_preview": sample_series(stationary_series, 120),
                "model_comparison": [
                    {
                        "model": f"ARIMA Biasa({best_order[0]},{best_order[1]},{best_order[2]})",
                        "mae": standard_metrics["mae"],
                        "mse": standard_metrics["mse"],
                        "rmse": standard_metrics["rmse"],
                        "r2": standard_metrics["r2"],
                    },
                    {
                        "model": f"Rolling ARIMA({best_order[0]},{best_order[1]},{best_order[2]})",
                        "mae": rolling_metrics["mae"],
                        "mse": rolling_metrics["mse"],
                        "rmse": rolling_metrics["rmse"],
                        "r2": rolling_metrics["r2"],
                    },
                ],
            },
            "summary": build_summary(
                best_order,
                model_result["candidate_count"],
                len(series),
                rolling_metrics,
                args.forecast_steps,
            ),
            "forecast": forecast,
        }

        print(json.dumps(payload))
    except Exception as exc:
        print(
            json.dumps(
                {
                    "status": "error",
                    "message": str(exc),
                }
            )
        )
        sys.exit(1)


if __name__ == "__main__":
    main()
