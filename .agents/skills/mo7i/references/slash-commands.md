# MO7I Universal Intelligence — Official Slash Commands System

This reference defines the architecture, platform adapters, command specifications, and security model for the **Official Slash Commands System** in MO7I Universal Intelligence (v2.2.0).

---

## 1. Architectural Philosophy

> **One unified intelligence with multiple direct entry points.**

Rather than treating slash commands as disjointed text macros or static prompts, MO7I implements a native **Command Router** that dispatches slash commands directly into its core intelligence engines:
- Universal Intelligence Meta-Orchestrator (16-phase lifecycle)
- Project Intelligence & Automated Skill Setup (`setup-engine.js`)
- Skill Learning Engine (4-phase loop)
- `find-skills` Dynamic Discovery Engine
- Health Diagnostics & Doctor

```text
               User Slash Command Interface
          (/mo7i, /setup-project-skills, /mo7i audit)
                            │
                            ▼
          ┌───────────────────────────────────┐
          │     Platform Command Adapters     │
          │ (Claude Code, Cursor, Codex, CLI) │
          └─────────────────┬─────────────────┘
                            │
                            ▼
          ┌───────────────────────────────────┐
          │        MO7I Command Router        │
          │    (Validation & Traversal Guard) │
          └─────────────────┬─────────────────┘
                            │
       ┌──────────────┬─────┴────────┬──────────────┬──────────────┐
       ▼              ▼              ▼              ▼              ▼
┌──────────────┐┌──────────────┐┌──────────────┐┌──────────────┐┌──────────────┐
│  Universal   ││   Project    ││   Learning   ││    Skill     ││  Diagnostics │
│ Task Engine  ││ Skill Setup  ││    Engine    ││  Discovery   ││   & Doctor   │
│   (/mo7i)    ││ (/setup-...) ││(/mo7i learn) ││(/mo7i disc.) ││(/mo7i doctor)│
└──────────────┘└──────────────┘└──────────────┘└──────────────┘└──────────────┘
```

---

## 2. Official Command Registry

### A. `/mo7i [task]`
- **Purpose**: Primary autonomous entry point to the Universal Intelligence engine.
- **Behavior**:
  - If invoked without arguments: displays command summary and help table.
  - If invoked with a natural-language task: inspects project context, checks setup status, classifies functional domain, formulates execution roadmap, coordinates specialist skills, and executes with verifiable evidence.
- **Examples**:
  ```text
  /mo7i Build a modern Next.js landing page with Tailwind CSS.
  /mo7i Audit my project security and container configurations.
  /mo7i Research high-concurrency database architectures.
  /mo7i Improve the SEO and OpenGraph metadata of this website.
  ```

### B. `/mo7i help`
- **Purpose**: Displays the complete catalog of commands, descriptions, parameters, and examples.
- **Safety**: Read-only (zero disk modifications).

### C. `/setup-project-skills [mode] [flags]`
- **Purpose**: Direct invocation of `mo7i-project-setup`.
- **Modes**:
  - `initial`: Complete technical stack analysis and project skill provisioning.
  - `audit`: Read-only verification of installed skills vs project requirements.
  - `update`: Safe detection and installation of newly required skills when dependencies change.
  - `repair`: Reinstallation of missing or corrupted skills from lockfiles.
  - `report`: Executive markdown report of project capabilities.
- **Flags**: `--dry-run`, `--project <path>`, `--all`
- **Scope**: Strictly project-scoped (`./.agents/skills/`), never global (`-g`).

### D. `/mo7i audit`
- **Purpose**: Comprehensive, read-only audit of current project dependencies, installed skills, compatibility matrix, and missing capabilities.
- **Safety**: Read-only.

### E. `/mo7i learn [skill-name]`
- **Purpose**: Executes the 4-phase learning loop (Inspect -> Understand -> Integrate -> Validate) on installed or discovered skills.
- **Safety**: Updates knowledge database; non-destructive.

### F. `/mo7i discover <query>`
- **Purpose**: Searches `skills.sh` via `find-skills` and scores candidate specialists across 5 dimensions (Stack Fit 35%, Reputation 25%, Adoption 15%, Docs 15%, Security 10%).
- **Safety**: Read-only; does not install skills automatically.

### G. `/mo7i status`
- **Purpose**: Displays system release version, active workspace, detected stack archetype, installed skills inventory, setup status, and active engines.
- **Safety**: Read-only (`--json` flag supported).

### H. `/mo7i doctor`
- **Purpose**: Runs diagnostic checks on Node.js runtime, Skills CLI availability, command registrations in `.claude/commands/`, script integrity, and project registry consistency.
- **Safety**: Read-only diagnostics separated from repairs.

### I. `/mo7i update`
- **Purpose**: Checks for dependency additions, newly recommended skills, and registry synchronization.
- **Safety**: Requires explicit approval before modifying packages; automated `.bak` backup generation.

---

## 3. Platform Compatibility & Discovery

MO7I provides native command registrations for each supported agent platform:

| Platform | Discovery Location | Native Mechanism |
| :--- | :--- | :--- |
| **Claude Code** | `.claude/commands/` and `~/.claude/commands/` | Native Markdown slash commands (`mo7i.md`, `setup-project-skills.md`) |
| **Cursor & Windsurf** | `.cursor/rules/` and `.cursorrules` | MDC rule-based command dispatch (`mo7i-commands.mdc`) |
| **OpenAI Codex** | `.codex/prompts/` | Prompt command files |
| **Gemini CLI** | `.gemini/commands/` | Command extensions |
| **Universal Shell & Agent** | `bin/mo7i`, `bin/setup-project-skills` | Executable Node.js binary via `npx mo7i` or local path |

---

## 4. Input Validation & Security Model

1. **Path Traversal Protection**: All `--project` flags pass through `checkPathTraversal()`, blocking attempts to traverse directories (`../../`) or reference root system paths (`/etc/`, `/var/`).
2. **Read-Only Strictness**: Inspection commands (`audit`, `status`, `doctor`, `help`, `discover`) are guaranteed non-destructive and make zero filesystem modifications.
3. **Approval Gates**: Updates or package additions require explicit interactive user authorization.
4. **Scope Isolation**: Prevents accidental global (`-g`) installations during project setup.
5. **Zero Secret Persistence**: Automatic scrubbing replaces credentials and tokens with `[REDACTED_BY_MO7I_SECURITY]`.
