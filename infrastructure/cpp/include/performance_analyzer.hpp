#pragma once

#include <map>
#include <string>
#include <vector>

namespace infra {

struct PerformanceSample {
    std::string component;
    double duration_ms{};
};

class PerformanceAnalyzer {
  public:
    std::map<std::string, double> aggregate(const std::vector<PerformanceSample> &samples) const;
};

} // namespace infra
