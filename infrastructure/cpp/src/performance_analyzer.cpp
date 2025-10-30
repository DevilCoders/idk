#include "performance_analyzer.hpp"

#include <algorithm>

namespace infra {

std::map<std::string, double>
PerformanceAnalyzer::aggregate(const std::vector<PerformanceSample> &samples) const {
    std::map<std::string, double> totals;
    for (const auto &sample : samples) {
        totals[sample.component] += sample.duration_ms;
    }
    return totals;
}

} // namespace infra
