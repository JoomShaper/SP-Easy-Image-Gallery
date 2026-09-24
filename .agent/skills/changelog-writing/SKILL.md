---
name: changelog-writing
description: Write concise, professional software changelogs using consistent prefixes and release-note style.
---

# Changelog Writing

Write concise, professional changelog entries for software releases.

## Rules

- Keep entries short, clear, and user-focused.
- Use exactly one prefix for every changelog item:
  - `**New:**` — new features or functionality
  - `**Update:**` — improvements, enhancements, or behavior changes
  - `**Fix:**` — bug fixes and corrections
  - `**Security:**` — security fixes, hardening, permission improvements, or security-related changes
- Never use more than one prefix on the same item.
- Do not create separate `New`, `Update`, `Fix`, or `Security` sections unless explicitly requested.
- Avoid unnecessary implementation details.
- Use natural, professional English.
- Preserve product names and feature names.
- Keep `(Pro Only)` when a feature is available only in the Pro version.
- For security issues, clearly describe the security improvement without unnecessary exploit details.
- For bug fixes, describe the problem or the resulting improvement clearly.
- Avoid vague wording such as:
  - "Various improvements"
  - "Some fixes"
  - "Minor changes"
- Prefer active, concise wording.
- Do not invent changes that were not provided.

## Format

Use:

# Product vX.X.X

- **New:** Added ...
- **Update:** Improved ...
- **Fix:** Resolved ...
- **Security:** Strengthened ...

## Examples

- **New:** Added multi-currency support.
- **New:** Added configurable product comparison.
- **Update:** Improved the checkout layout and guest account creation.
- **Fix:** Resolved order ID mismatch issue.
- **Security:** Strengthened permission checks for administrative actions.
