from pathlib import Path

from .generator import ClientTarget, GenerationRequest, PolyglotGenerator, load_api_spec


def main() -> None:
    spec_path = Path("config/api_spec.json")
    api_spec = load_api_spec(spec_path)
    request = GenerationRequest(
        service_name=api_spec["name"],
        api_spec=api_spec,
        clients=[
            ClientTarget("python", Path("generated/python_client.json")),
            ClientTarget("go", Path("generated/go_client.json")),
            ClientTarget("php", Path("generated/php_client.json")),
        ],
        documentation_path=Path("generated/README.md"),
    )
    generator = PolyglotGenerator(Path.cwd())
    artifacts = generator.generate(request)
    print("Generated artifacts:")
    for artifact in artifacts:
        print(f" - {artifact}")


if __name__ == "__main__":
    main()
