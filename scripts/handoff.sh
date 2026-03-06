#!/usr/bin/env bash

set -u

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
HANDOFF_FILE="$REPO_ROOT/HANDOFF.md"
TMP_FILE="$HANDOFF_FILE.tmp"

extract_section() {
  local heading="$1"
  local file="$2"
  awk -v heading="$heading" '
    $0 == "## " heading {in_section=1; print; next}
    in_section && /^## / {exit}
    in_section {print}
  ' "$file"
}

default_current_objective() {
  cat <<'EOF'
## Current objective
- Keep this section up to date before ending a session. State the single main objective for the next person/machine.
EOF
}

default_next_steps() {
  cat <<'EOF'
## Next steps
- [ ] Add the next actionable step here.
- [ ] Add any follow-up checks or tests.
EOF
}

branch="unknown"
last_commit_hash="none"
last_commit_subject="No commits yet"
working_tree_status="unknown"
changed_files="- None"
diffstat="N/A"
recent_commits="- None"
commits_today="- None"
git_context_note=""

if ! command -v git >/dev/null 2>&1; then
  git_context_note="Git is not available in PATH. Snapshot sections are limited."
else
  if ! git -C "$REPO_ROOT" rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    git_context_note="This directory is not a Git repository. Snapshot sections are limited."
  else
    branch="$(git -C "$REPO_ROOT" branch --show-current 2>/dev/null)"
    [ -n "$branch" ] || branch="detached HEAD"

    if git -C "$REPO_ROOT" rev-parse --verify HEAD >/dev/null 2>&1; then
      last_commit_hash="$(git -C "$REPO_ROOT" rev-parse --short HEAD)"
      last_commit_subject="$(git -C "$REPO_ROOT" log -1 --pretty=%s)"
      recent_commits="$(git -C "$REPO_ROOT" log --oneline -10 | sed 's/^/- /')"
      commits_today_raw="$(git -C "$REPO_ROOT" log --since='00:00' --oneline)"
      if [ -n "$commits_today_raw" ]; then
        commits_today="$(printf "%s\n" "$commits_today_raw" | sed 's/^/- /')"
      fi
      diffstat_raw="$(git -C "$REPO_ROOT" diff --shortstat HEAD)"
      if [ -n "$diffstat_raw" ]; then
        diffstat="$diffstat_raw"
      fi
    else
      recent_commits="- No commits yet in this repository."
      commits_today="- No commits yet in this repository."
      diffstat_raw="$(git -C "$REPO_ROOT" diff --shortstat)"
      if [ -n "$diffstat_raw" ]; then
        diffstat="$diffstat_raw"
      fi
    fi

    status_porcelain="$(git -C "$REPO_ROOT" status --porcelain)"
    if [ -z "$status_porcelain" ]; then
      working_tree_status="clean"
    else
      working_tree_status="dirty"
      changed_files="$(printf "%s\n" "$status_porcelain" | sed 's/^/- /')"
    fi
  fi
fi

current_objective_section="$(default_current_objective)"
next_steps_section="$(default_next_steps)"

if [ -f "$HANDOFF_FILE" ]; then
  extracted_current="$(extract_section "Current objective" "$HANDOFF_FILE")"
  extracted_next="$(extract_section "Next steps" "$HANDOFF_FILE")"

  if [ -n "$extracted_current" ]; then
    current_objective_section="$extracted_current"
  fi
  if [ -n "$extracted_next" ]; then
    next_steps_section="$extracted_next"
  fi
fi

{
  printf "%s\n\n" "$current_objective_section"
  printf "%s\n\n" "$next_steps_section"
  cat <<EOF
## Auto-generated session snapshot
This section is updated by \`bash scripts/handoff.sh\`. Do not edit manually.

<!-- AUTO-GENERATED:START -->
### What changed today
$commits_today

### Current branch + last commit hash
- Branch: \`$branch\`
- Last commit: \`$last_commit_hash\` - $last_commit_subject

### Working tree status
- Status: \`$working_tree_status\`

### Changed files
$changed_files

### Diffstat
\`$diffstat\`

### Recent commits (last 10)
$recent_commits

### How to run locally (commands + URLs)
\`\`\`bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan optimize:clear
php artisan serve
npm run dev
\`\`\`
- App: \`http://127.0.0.1:8000\`
- Admin login: \`http://127.0.0.1:8000/admin/login\`
- Mailpit UI (optional): \`http://127.0.0.1:8025\`

### Known issues / blockers
EOF
  if [ -n "$git_context_note" ]; then
    printf -- "- %s\n" "$git_context_note"
  else
    printf -- "- None detected automatically. Add manual blockers to \`## Current objective\` if needed.\n"
  fi
  cat <<'EOF'

### If you're picking this up on another machine
- `git pull --rebase`
- `composer install` (if `vendor/` is missing or lock file changed)
- `npm install` (if `node_modules/` is missing or lock file changed)
- `cp .env.example .env` (first time only; never commit `.env`)
- `php artisan migrate`
- `php artisan optimize:clear`
- Start servers: `php artisan serve` and `npm run dev`
<!-- AUTO-GENERATED:END -->
EOF
} > "$TMP_FILE"

mv "$TMP_FILE" "$HANDOFF_FILE"
printf "Updated handoff: %s\n" "$HANDOFF_FILE"
