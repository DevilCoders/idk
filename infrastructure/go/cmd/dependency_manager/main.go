package main

import (
    "encoding/json"
    "fmt"
    "os"
    "path/filepath"
)

type Dependency struct {
    Name    string `json:"name"`
    Version string `json:"version"`
    License string `json:"license"`
}

type Manifest struct {
    Services     []string     `json:"services"`
    Dependencies []Dependency `json:"dependencies"`
}

func main() {
    configRoot := os.Getenv("CONFIG_ROOT")
    if configRoot == "" {
        configRoot = filepath.Join("..", "..", "config")
    }
    manifestPath := filepath.Join(configRoot, "dependency_manifest.json")
    raw, err := os.ReadFile(manifestPath)
    if err != nil {
        panic(fmt.Errorf("cannot read manifest: %w", err))
    }

    var manifest Manifest
    if err := json.Unmarshal(raw, &manifest); err != nil {
        panic(fmt.Errorf("invalid manifest: %w", err))
    }

    fmt.Println("Unified Dependency Report")
    fmt.Println("==========================")
    for _, dep := range manifest.Dependencies {
        fmt.Printf("%s %s (license: %s)\n", dep.Name, dep.Version, dep.License)
    }
}
