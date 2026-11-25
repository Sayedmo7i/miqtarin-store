# Autonomous Orchestration Lifecycle & Execution Protocol

This document specifies the augmented execution lifecycle, project intelligence integration, multi-skill coordination protocols, and cross-domain task orchestration implemented by MO7I Universal Intelligence (v2.1.0).

---

## 1. Core Operating Philosophy

> **The user specifies WHAT needs to be accomplished. MO7I determines HOW to accomplish it.**

MO7I Universal Intelligence acts as a meta-orchestrator. It translates high-level user intentions into structured, evidence-based execution phases, dynamically establishing foundational project intelligence and applying specialist domain skills without requiring the user to manually search for or configure individual tools.

---

## 2. The Integrated Autonomous Lifecycle

In v2.1.0, the lifecycle incorporates the **Project Intelligence Gate** prior to task-level discovery and execution:

```
[1. INTAKE] ──► [2. PROJECT DETECTION] ──► [3. PROJECT PROFILE INSPECTION]
                                                      │
[6. TASK CLASSIFICATION] ◄── [5. PROJECT SETUP (WHEN NEEDED)] ◄── [4. SETUP STATUS CHECK]
         │
         ▼
[7. CAPABILITY GAP ANALYSIS] ──► [8. SKILL DISCOVERY] ──► [9. SKILL LEARNING]
                                                              │
[12. EXECUTION] ◄── [11. SKILL ORCHESTRATION] ◄── [10. COMPATIBILITY & PLANNING]
         │
         ▼
[13. QUALITY VALIDATION] ──► [14. KNOWLEDGE & PROFILE UPDATE] ──► [15. SAFE CLEANUP] ──► [16. FINAL REPORT]
```

### Detailed Phase Breakdown

1. **INTAKE**: Receive user prompt, extract explicit objectives, deliverables, and hard constraints.
2. **PROJECT DETECTION**: Identify project root, git repository boundaries, environment runtimes, and repository layout.
3. **PROJECT PROFILE INSPECTION**: Audit `.mo7i/project-profile.json` and `.mo7i/skill-registry.json` for existing project intelligence.
4. **SETUP STATUS CHECK**: Determine whether the project is uninitialized, missing foundational stack skills, or has experienced dependency drift.
5. **PROJECT SKILL SETUP (WHEN NEEDED)**: Invoke companion skill `mo7i-project-setup` in the appropriate mode (`initial`, `update`, or `repair`):
   - Integrates the verified official Matt Pocock collection (`mattpocock/skills`).
   - Discovers and installs essential core framework specialists (Next.js, Flutter, Laravel, etc.).
   - Establishes persistent project scope in `./.agents/skills/` without global `-g` flags.
   - Generates `.mo7i/project-profile.json`, `.mo7i/skill-registry.json`, and `.mo7i/setup-report.md`.
6. **TASK CLASSIFICATION**: Categorize the immediate task into one of the 13 functional domains and assess complexity tier.
7. **CAPABILITY GAP ANALYSIS**: Determine whether project-installed foundational skills suffice or if a task-level transient skill is needed.
8. **SKILL DISCOVERY**: If task-specific specialist intelligence is missing, invoke Vercel Labs `find-skills` (`npx skills find "<keywords>"`).
9. **SKILL LEARNING**: Execute the 4-phase learning loop (Inspect -> Understand -> Integrate -> Validate) before using any selected skill.
10. **COMPATIBILITY & PLANNING**: Verify candidate skills do not conflict with project conventions and construct phased roadmap.
11. **SKILL ORCHESTRATION**: Sequence specialist skills across pipeline stages, minimizing context by loading only the skill needed for the active phase.
12. **EXECUTION**: Apply domain best practices and execute implementation commands with real tools.
13. **QUALITY VALIDATION**: Run test suites, compilers, static analyzers, and linters. Enforce "evidence over claims".
14. **KNOWLEDGE & PROFILE UPDATE**: Record operational findings in user memory and update project profile telemetry.
15. **SAFE CLEANUP**: Prune transient task skills while strictly preserving project foundational skills and user-authored skills.
16. **FINAL REPORT**: Deliver structured final report citing verified outputs, changed files, and standardized status labels (`PASS`, `FAIL`, `BLOCKED`).

---

## 3. Project-Level vs. Task-Level Skill Management

A critical invariant in MO7I v2.1.0 is the clear separation between persistent project-level skills and ephemeral task-level skills:

| Dimension | Project-Level Skills | Task-Level Discovered Skills |
| :--- | :--- | :--- |
| **Trigger** | Project initialization, technology stack drift, or user setup request | Immediate task requires niche capability not covered by project skills |
| **Scope** | Persistent repository environment (`<projectRoot>/.agents/skills/`) | Transient execution scope / temporary workspace |
| **Catalog** | Matt Pocock collection + Core stack specialists | Niche external community skills |
| **Registry** | Recorded in `.mo7i/skill-registry.json` and `skills-lock.json` | Recorded as temporary in runtime memory |
| **Cleanup** | **Preserved indefinitely** across all future turns | Pruned at Phase 15 if marked temporary |

---

## 4. Conflict Resolution & Precedence Hierarchy

When directives between user prompts, repository rules, and specialist skills conflict, the orchestrator enforces an explicit 6-level descending precedence hierarchy:

```
┌────────────────────────────────────────────────────────────────────────┐
│ 1. System & Platform Safety Requirements (HIGHEST AUTHORITY)           │
│    Sandbox boundaries, secret protection, destructive action blocks.   │
├────────────────────────────────────────────────────────────────────────┤
│ 2. Explicit Current User Instructions                                  │
│    Direct prompt overrides for the immediate turn.                     │
├────────────────────────────────────────────────────────────────────────┤
│ 3. Project-Specific Instructions                                       │
│    Repository conventions (AGENTS.md, CLAUDE.md, linters, configs).    │
├────────────────────────────────────────────────────────────────────────┤
│ 4. Specialist Skill Instructions                                       │
│    Domain-specific guidelines from the actively loaded specialist.    │
├────────────────────────────────────────────────────────────────────────┤
│ 5. Confirmed User Preferences                                          │
│    Preferences confirmed in ~/.config/mo7i/universal-intelligence/.    │
├────────────────────────────────────────────────────────────────────────┤
│ 6. General Defaults (LOWEST AUTHORITY)                                 │
│    Built-in agent heuristics and fallbacks.                            │
└────────────────────────────────────────────────────────────────────────┘
```
