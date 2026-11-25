# Project Intelligence & Skill Setup Architecture

This reference provides technical specifications, architectural decisions, and operational guidelines for **MO7I Project Intelligence & Automated Skill Setup** (v2.1.0).

---

## 1. Project-Level vs. Global Skills Architecture

A fundamental architectural distinction governs skill management in MO7I Universal Intelligence:

```
┌─────────────────────────────────────────────────────────────┐
│                    GLOBAL SKILL LAYER                       │
│ Location: ~/.agents/skills/ or ~/.config/mo7i/              │
│ Scope: Foundational Agent Meta-Skills & User Memory         │
│ Example: mo7i-universal-intelligence, find-skills           │
└──────────────────────────────┬──────────────────────────────┘
                               │
       ┌───────────────────────┴───────────────────────┐
       ▼                                               ▼
┌─────────────────────────────┐         ┌─────────────────────────────┐
│     PROJECT A WORKSPACE     │         │     PROJECT B WORKSPACE     │
│ Stack: Next.js + TypeScript │         │ Stack: Flutter + Dart       │
│ Scope: ./.agents/skills/    │         │ Scope: ./.agents/skills/    │
│ Registry: .mo7i/            │         │ Registry: .mo7i/            │
│ Local Skills:               │         │ Local Skills:               │
│ - nextjs-best-practices     │         │ - flutter-specialist        │
│ - setup-ts-deep-modules     │         │ - mobile-ui-ux              │
│ - Matt Pocock Collection    │         │ - Matt Pocock Collection    │
└─────────────────────────────┘         └─────────────────────────────┘
```

### Key Distinctions:

| Attribute | Project-Scoped Skills | Global Skills | Task-Level Transient Skills |
| :--- | :--- | :--- | :--- |
| **Location** | `<projectRoot>/.agents/skills/` | `~/.agents/skills/` | Memory / Temp workspace |
| **Persistence** | Permanent for repository lifecycle | Permanent across all projects | Ephemeral (duration of single task) |
| **Lockfile** | `<projectRoot>/skills-lock.json` | Global agent config | Pruned at cleanup phase |
| **Purpose** | Foundational stack specialists (Next.js, Flutter, Matt Pocock collection) | Universal orchestrator & discovery tools | One-off niche tools (e.g. esoteric compiler) |
| **Installation Syntax** | `npx skills add <pkg> --skill <name> -y` | `npx skills add <pkg> -g -y` | Direct context load / cleanup |

**Rule of Isolation**: Project setup MUST NEVER pass `-g` or `--global` to the Skills CLI. Project skills remain isolated to that specific repository.

---

## 2. Matt Pocock Skills Collection Integration

The repository owner has integrated the official, verified **Matt Pocock Skills Collection** into MO7I's automated setup.

### Official Source Verification
- **Publisher**: Matt Pocock (`mattpocock`)
- **Git Remote**: `https://github.com/mattpocock/skills.git`
- **Registry Endpoint**: `https://skills.sh/mattpocock/skills`
- **Package Identifier**: `mattpocock/skills`
- **Discovered Skill Count**: 39
- **Verified Usable Count**: 38
- **Upstream Skipped Count**: 1

### Detailed Upstream Exception:
- **Skill**: `software-factory`
- **Path**: `skills/in-progress/software-factory/SKILL.md`
- **Root Cause**: Upstream YAML syntax error on line 2 (nested mapping in compact format: `description: Shared language for designing a software factory: the tasks it run...`).
- **Resolution**: Skipped safely during automated enumeration with explicit logging in `.mo7i/setup-report.md`.

### Complete 38-Skill Roster by Category:

#### Development & Architecture (25 Skills)
1. `ask-matt` — Router over the skills collection.
2. `codebase-design` — Deep modules design and interface narrowing.
3. `domain-modeling` — Domain terminology, GLOSSARY.md, and ADRs.
4. `grill-with-docs` — Relentless interview generating ADRs and glossary.
5. `implement` — Spec-driven implementation.
6. `implement-spec` — Code implementation directly from tickets.
7. `improve-codebase-architecture` — Automated scan and visual HTML reporting for deepening opportunities.
8. `pr` — High-clarity, evidence-based PR descriptions.
9. `prototype` — Rapid throwaway prototyping.
10. `research` — Primary-source technical research.
11. `retro` — Coding session retrospectives.
12. `setup-matt-pocock-skills` — Issue tracker and label vocabulary configuration.
13. `to-spec` — Synthesis of conversation into technical specifications.
14. `to-tickets` — Tracer-bullet tickets with dependency edges.
15. `triage` — State-machine issue and PR triage.
16. `wayfinder` — Milestone planning via decision tickets.
17. `wizard` — Interactive bash wizard for human-gated steps.
18. `grill-me` — Plan stress-testing interview.
19. `grilling` — Relentless grilling triggers.
20. `handoff` — Agent-to-agent session compaction.
21. `teach` — Conceptual instruction within the workspace.
22. `to-questionnaire` — Stakeholder decision questionnaire.
23. `wait-what` — Immediate repitch of concepts that missed the mark.
24. `writing-for-agents` — Documentation formatted for autonomous agents.
25. `chief-of-staff` — Single-session goal pursuit coordinating sub-agents.
26. `claude-handoff` — Background agent handoff.
27. `loop-me` — Interactive workflow spec interview.
28. `writing-beats` — Sequential beat grounding.
29. `writing-fragments` — Unstructured exploratory fragments.
30. `writing-shape` — Structured article shaping.

#### Quality Assurance & Safety (5 Skills)
31. `code-review` — Parallel Standards and Spec axis review sub-agents.
32. `diagnosing-bugs` — Root-cause diagnosis loop for regressions.
33. `tdd` — Test-driven development (red-green-refactor).
34. `git-guardrails-claude-code` — Hooks blocking dangerous git commands.
35. `setup-pre-commit` — Husky pre-commit hooks with lint-staged and tests.

#### Project-Specific & Optional (3 Skills)
36. `setup-ts-deep-modules` — Dependency-cruiser architectural boundaries for TypeScript.
37. `migrate-to-shoehorn` — Safe test type casting with @total-typescript/shoehorn.
38. `scaffold-exercises` — Scaffolding structured exercise directories.

---

## 3. Project Intelligence Registry Formats

Every project initialized by MO7I maintains three persistent records under `<projectRoot>/.mo7i/`:

### 1. `project-profile.json`
Captures technical stack and operational requirements:
```json
{
  "id": "my-web-app",
  "path": "/root/projects/my-web-app",
  "type": "Fullstack Web Application (Next.js)",
  "languages": ["TypeScript", "JavaScript"],
  "frameworks": [
    { "name": "Next.js", "version": "^15.1.0" },
    { "name": "Tailwind CSS", "version": "^3.4.0" }
  ],
  "databases": ["Prisma ORM"],
  "architecture": "Next.js App Router (Server Actions & Components)",
  "testFrameworks": ["Vitest", "Playwright (E2E)"],
  "deployment": "Vercel / Cloud Container",
  "securityRequirements": ["Zero-trust secret management"],
  "codeQualityConventions": ["ESLint", "Prettier"],
  "existingTools": ["Husky Git Hooks", "GitHub Actions (2 workflows)"],
  "existingSkills": ["ask-matt", "codebase-design", "tdd"],
  "inspectedFiles": ["package.json", "tsconfig.json", "app", ".github/workflows"],
  "setupStatus": "INITIALIZED",
  "setupVersion": "2.1.0",
  "analyzedAt": "2026-11-15T18:00:00.000Z",
  "lastAuditAt": "2026-11-15T18:00:00.000Z"
}
```

### 2. `skill-registry.json`
Catalogs installed project skills, origins, and missing recommendations:
```json
{
  "version": "2.1.0",
  "projectId": "my-web-app",
  "projectPath": "/root/projects/my-web-app",
  "scope": "project",
  "updatedAt": "2026-11-15T18:00:00.000Z",
  "mattPocockCollection": {
    "officialSource": "https://github.com/mattpocock/skills.git",
    "package": "mattpocock/skills",
    "totalDiscovered": 39,
    "verifiedCount": 38,
    "skippedCount": 1,
    "skippedDetails": [
      {
        "skill": "software-factory",
        "path": "skills/in-progress/software-factory/SKILL.md",
        "reason": "Upstream YAML syntax error (nested mapping without indentation in frontmatter description)"
      }
    ]
  },
  "installedSkills": [
    {
      "id": "nextjs-best-practices",
      "package": "sickn33/agentic-awesome-skills@nextjs-best-practices",
      "source": "skills.sh",
      "category": "CORE_PROJECT_SKILLS",
      "scope": "project",
      "installedAt": "2026-11-15T18:00:00.000Z"
    },
    {
      "id": "codebase-design",
      "package": "mattpocock/skills",
      "source": "mattpocock/skills",
      "category": "DEVELOPMENT",
      "scope": "project",
      "installedAt": "2026-11-15T18:00:00.000Z"
    }
  ],
  "missingRecommendedSkills": []
}
```

### 3. `setup-report.md`
Human-readable Markdown document summarizing project architecture, installed specialists, and security status.

---

## 4. Operating Modes & Lifecycle

```
                           ┌───────────────────────────┐
                           │   PROJECT ENCOUNTERED     │
                           └─────────────┬─────────────┘
                                         ▼
                           ┌───────────────────────────┐
                           │  INSPECT .mo7i/ REGISTRY  │
                           └─────────────┬─────────────┘
                                         │
                 ┌───────────────────────┴───────────────────────┐
                 ▼                                               ▼
         [Registry Missing]                             [Registry Present]
                 │                                               │
                 ▼                                               ▼
     ┌───────────────────────┐                       ┌───────────────────────┐
     │     INITIAL SETUP     │                       │      AUDIT CHECK      │
     │ - Analyze stack       │                       │ - Check drift         │
     │ - Install Pocock pack │                       │ - Check missing deps  │
     │ - Install core skills │                       └───────────┬───────────┘
     │ - Write .mo7i data    │                                   │
     └───────────────────────┘                   ┌───────────────┴───────────────┐
                                                 ▼                               ▼
                                         [Stack Changed]                  [Files Missing]
                                                 │                               │
                                                 ▼                               ▼
                                     ┌───────────────────────┐       ┌───────────────────────┐
                                     │     UPDATE MODE       │       │     REPAIR MODE       │
                                     │ - Discover new skills │       │ - Reinstall missing   │
                                     │ - Add to project      │       │   skills from lockfile│
                                     │ - Update registry     │       └───────────────────────┘
                                     └───────────────────────┘
```

1. **INITIAL SETUP**: Triggers when a repository has no `.mo7i/project-profile.json`. Probes dependencies, runs candidate scoring, provisions project-scoped skills, and saves registry records.
2. **AUDIT**: Non-destructive. Audits current `package.json`, `pubspec.yaml`, or `composer.json` against `.mo7i/skill-registry.json`. Emits gap analysis without modifying files.
3. **UPDATE**: Triggered when a project introduces new dependencies (e.g., adding `prisma` to an existing Next.js repo). Installs newly recommended specialist skills and updates `.mo7i/skill-registry.json` with a `.bak` backup.
4. **REPAIR**: Verifies disk integrity. If a skill folder in `.agents/skills/` was accidentally deleted or damaged, re-provisions it using `skills-lock.json`.
5. **REPORT**: Formats the project intelligence inventory for user presentation.

---

## 5. Security & Zero-Secret Guardrails

1. **Zero Secret Persistence**: All objects written to `.mo7i/` pass through `sanitizeObject()`, which redacts keys containing `key`, `secret`, `token`, `password`, `bearer`, or `credential`.
2. **Pre-Installation Threat Scans**: Skills undergo AST and regex scanning before installation. Attempts to exfiltrate `~/.aws/credentials`, `~/.ssh/id_rsa`, or execute `rm -rf /` are blocked.
3. **Strict Scope Enforcement**: The orchestrator prohibits `-g` or `--global` flags during project setup.
4. **User Skill Protection**: Custom skills residing in `.agents/skills/` are recognized as non-deletable and non-overwritable assets.
