"""Predictive analytics and project health scoring."""

from __future__ import annotations

from dataclasses import dataclass
from statistics import mean
from typing import Iterable, List


@dataclass(frozen=True)
class MetricSample:
    name: str
    values: List[float]

    def average(self) -> float:
        return mean(self.values) if self.values else 0.0


@dataclass(frozen=True)
class PredictiveInsight:
    metric: str
    score: float
    recommendation: str


class InsightEngine:
    """Tiny ML-inspired scoring engine for demonstration."""

    def generate(self, samples: Iterable[MetricSample]) -> List[PredictiveInsight]:
        insights: List[PredictiveInsight] = []
        for sample in samples:
            avg = sample.average()
            if avg < 0.5:
                recommendation = "High risk detected: increase test coverage and refactor hotspots."
            elif avg < 0.8:
                recommendation = "Moderate risk: schedule architecture review and dependency updates."
            else:
                recommendation = "Healthy trajectory: continue automated optimization experiments."
            insights.append(PredictiveInsight(sample.name, avg, recommendation))
        return insights


__all__ = ["MetricSample", "PredictiveInsight", "InsightEngine"]
