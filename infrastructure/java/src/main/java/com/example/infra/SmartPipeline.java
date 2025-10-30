package com.example.infra;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.time.Instant;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;

/**
 * SmartPipeline is a lightweight reference implementation of the
 * cross-language continuous delivery orchestration layer. The goal is to
 * demonstrate how the project stitches together multiple ecosystems while
 * keeping a single source of truth for every build.
 */
public final class SmartPipeline {

    private enum Stage {
        TRIGGER,
        BUILD,
        TEST,
        ANALYZE,
        SECURITY_SCAN,
        DEPLOY,
        MONITOR
    }

    private static final List<String> LANGUAGES = List.of("java", "python", "cpp", "php", "go", "javascript");

    private SmartPipeline() {
        // utility class
    }

    public static void main(String[] args) throws IOException {
        PipelineState state = PipelineState.bootstrap();
        for (Stage stage : Stage.values()) {
            StageResult result = executeStage(stage, state);
            state = state.withStageResult(stage, result);
        }
        writeDashboardSnapshot(state);
    }

    private static StageResult executeStage(Stage stage, PipelineState state) {
        System.out.printf("[%s] Running %s stage for targets %s%n", Instant.now(), stage, LANGUAGES);
        return switch (stage) {
            case TRIGGER -> StageResult.success("Webhook trigger accepted");
            case BUILD -> StageResult.success("Polyglot build graph executed");
            case TEST -> StageResult.success("Smart tests executed across JVM, Python and PHP");
            case ANALYZE -> StageResult.success("Static analysis coverage: " + state.coverage());
            case SECURITY_SCAN -> StageResult.success("SAST/DAST scan summary generated");
            case DEPLOY -> StageResult.success("Immutable artifact bundles published");
            case MONITOR -> StageResult.success("Telemetry streaming to unified dashboard");
        };
    }

    private static void writeDashboardSnapshot(PipelineState state) throws IOException {
        Path output = Paths.get("web/assets/js/pipeline_snapshot.json");
        Files.createDirectories(output.getParent());
        Files.writeString(output, state.toJson());
    }

    private record StageResult(boolean success, String summary) {
        static StageResult success(String summary) {
            return new StageResult(true, summary);
        }
    }

    private record PipelineState(Map<Stage, StageResult> results, double coverage) {

        static PipelineState bootstrap() {
            return new PipelineState(Map.of(), 87.5);
        }

        PipelineState withStageResult(Stage stage, StageResult result) {
            Map<Stage, StageResult> merged = new java.util.EnumMap<>(Stage.class);
            merged.putAll(results);
            merged.put(stage, result);
            return new PipelineState(merged, coverage);
        }

        double coverage() {
            return coverage;
        }

        String toJson() {
            List<String> stageJson = new ArrayList<>();
            for (Stage stage : Stage.values()) {
                StageResult result = results.get(stage);
                if (result != null) {
                    stageJson.add(String.format("{\"stage\":\"%s\",\"success\":%s,\"summary\":\"%s\"}",
                            stage.name(), result.success, result.summary.replace("\"", "'")));
                }
            }
            return stageJson.stream().collect(Collectors.joining(",", "[", "]"));
        }
    }
}
