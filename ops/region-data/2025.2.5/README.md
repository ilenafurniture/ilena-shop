# Ilena region dataset snapshot

This immutable snapshot is included so local and production dry-runs use the
same input files. It is not an automatic source of truth; review the upstream
release and legal basis before replacing it.

- Dataset package checked: `@nusawilayah/data@2025.2.5` (published 2026-11-03).
- Package legal basis: Kepmendagri No. 300.2.2-2430 Tahun 2025.
- Upstream package: <https://www.npmjs.com/package/@nusawilayah/data>.
- Underlying public source: <https://github.com/cahyadsn/wilayah>.
- License: MIT; see the license and attribution notices in the SQL files.
- `wilayah.sql` normalized-LF SHA-256: `b90f420bf8f8027170168f33314c4f065ec422120531f16708ea2d833c6c2144`.
- `wilayah_kodepos.sql` normalized-LF SHA-256: `50fa6661dca14ed52ca604e34922f30b49e9b6b2328f5d3be9f9db31bbecfa4c`.
- The command normalizes CRLF/CR to LF before hashing so Windows and Linux dry-runs report identical integrity hashes.

The four hierarchy CSVs in that package were compared row-by-row with
`wilayah.sql`: 91,599 codes and names matched exactly. The package postal-code
CSV also matched `wilayah_kodepos.sql` for all 83,762 village codes. The
upstream project describes postal codes as best-effort; do not represent this
snapshot as an independently verified Pos Indonesia master.
