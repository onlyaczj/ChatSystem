# Contributing

Thank you for your interest in improving ChatSystem.

## Code style

- Use `declare(strict_types=1);` in PHP files.
- Keep managers focused on one responsibility.
- Keep commands inside `src/Aczj/ChatSystem/Command`.
- Keep listeners inside `src/Aczj/ChatSystem/Listener`.
- Keep configurable text inside `resources/config.yml`.

## Before opening a pull request

Run a syntax check:

```bash
find src -name "*.php" -print0 | xargs -0 -n1 php -l
```
