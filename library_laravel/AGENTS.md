# Project Agent Operating Guide

This project uses `ai-engineering` as the canonical policy source.

## Canonical Rule Sources (Mandatory)
- `ai-engineering/glopal-rules/engineering.md`
- `ai-engineering/reviews/code_review.md`
- `ai-engineering/workflows/feature-development.md`

All instructions in the files above are mandatory and should be treated as fully integrated project settings.
If any summarized rule conflicts with the source files, the source files win.

## Cursor Rules Integration
The following always-on Cursor rule files mirror and operationalize the policy:
- `.cursor/rules/engineering-global.mdc`
- `.cursor/rules/workflow-feature-delivery.mdc`
- `.cursor/rules/review-checklist.mdc`

These files are organized for fast enforcement during implementation, but do not replace the canonical source.

## Execution Expectations
- Follow architecture boundaries and quality/security constraints from canonical rules.
- Use the feature workflow sequence before and during implementation.
- Apply blocker-first review logic before considering style/preferences.
- Preserve maintainability: simple design, clear naming, explicit error handling, and tests.

## Priority and Conflict Resolution
1. Direct user instruction (unless unsafe/destructive without confirmation).
2. Canonical `ai-engineering` rules.
3. `.cursor/rules` operational summaries.
4. Local convenience preferences.

## Maintenance
When `ai-engineering` files are updated, update `.cursor/rules` to keep parity.
