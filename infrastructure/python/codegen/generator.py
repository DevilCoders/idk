"""Polyglot code generation utilities.

This module provides a declarative interface used by the smart pipeline to
materialize client SDKs, protocol schemas and documentation from a unified
service contract. The implementation is intentionally light weight, but the
structure mirrors what a production system would require.
"""

from __future__ import annotations

from dataclasses import dataclass
from pathlib import Path
from typing import Iterable, List

import json


@dataclass(frozen=True)
class ClientTarget:
    language: str
    output: Path


@dataclass(frozen=True)
class GenerationRequest:
    service_name: str
    api_spec: dict
    clients: Iterable[ClientTarget]
    documentation_path: Path


class PolyglotGenerator:
    """Entry point for code generation.

    In a real system this class would integrate protocol buffer compilers,
    OpenAPI tooling, ORM synchronization and documentation formatters. Here we
    capture the intent by emitting structured artifacts ready for downstream
    consumption.
    """

    def __init__(self, fs_root: Path) -> None:
        self._fs_root = fs_root

    def generate(self, request: GenerationRequest) -> List[Path]:
        artifacts: List[Path] = []
        for client in request.clients:
            artifact = self._write_client_stub(request.service_name, request.api_spec, client)
            artifacts.append(artifact)
        artifacts.append(self._write_documentation(request))
        return artifacts

    def _write_client_stub(self, service_name: str, api_spec: dict, client: ClientTarget) -> Path:
        data = {
            "service": service_name,
            "language": client.language,
            "endpoints": [endpoint["name"] for endpoint in api_spec.get("endpoints", [])],
        }
        client.output.parent.mkdir(parents=True, exist_ok=True)
        client.output.write_text(json.dumps(data, indent=2))
        return client.output

    def _write_documentation(self, request: GenerationRequest) -> Path:
        request.documentation_path.parent.mkdir(parents=True, exist_ok=True)
        request.documentation_path.write_text(
            f"# {request.service_name} API\n\n"
            f"Generated clients: {', '.join(client.language for client in request.clients)}\n"
            f"Operations: {len(request.api_spec.get('endpoints', []))}\n"
        )
        return request.documentation_path


def load_api_spec(path: Path) -> dict:
    """Load an API contract and return it as a Python dictionary."""

    return json.loads(path.read_text())


__all__ = [
    "ClientTarget",
    "GenerationRequest",
    "PolyglotGenerator",
    "load_api_spec",
]
