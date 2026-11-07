# Ilena region dataset snapshot

This immutable snapshot is included so local and production dry-runs use the
same input files. It is not an automatic source of truth; review the upstream
release and legal basis before replacing it.

- Dataset package checked: `@nusawilayah/data@2025.2.5` (published 2026-11-03).
- Package legal basis: Kepmendagri No. 300.2.2-2430 Tahun 2025.
- Upstream package: <https://www.npmjs.com/package/@nusawilayah/data>.
- Underlying public source: <https://github.com/cahyadsn/wilayah>.
- License: MIT; see the license and attribution notices in the SQL files.
- `wilayah.sql` SHA-256: `c4c3396d9380d4edee072af1d9dff83573b574d7cd00a6562cf82e200e954031`.
- `wilayah_kodepos.sql` SHA-256: `7ddc564b45582ffeaa469eddc756b9c0f09c77371e4756577c4b70e346ab8b2c`.

The four hierarchy CSVs in that package were compared row-by-row with
`wilayah.sql`: 91,599 codes and names matched exactly. The package postal-code
CSV also matched `wilayah_kodepos.sql` for all 83,762 village codes. The
upstream project describes postal codes as best-effort; do not represent this
snapshot as an independently verified Pos Indonesia master.
