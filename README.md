# ChatSystem

<p align="center">
  <img src="icon.png" width="180" alt="ChatSystem Icon">
</p>

<h1 align="center">ChatSystem</h1>

<p align="center">
  <b>A professional PocketMine-MP chat management plugin by aczj.</b><br>
  Built for PMMP API 5 with an embedded ECMD-style command layer.
</p>

---

## Overview

**ChatSystem** is a clean and configurable chat-control plugin for PocketMine-MP servers.  
It is designed to be easy to publish, easy to edit, and clean enough for a public GitHub repository.

The plugin includes cooldown control, chat locking, color-code control, capital-letter control, character limits, protected mentions, filtered words with anti-bypass normalization, and mention notification sounds.

## Features

- Main management command: `/chat`
- Embedded ECMD-style command layer inside `src/AEDXDEV/ECMD/`
- Message and command cooldown from `1` to `60` seconds
- Minecraft color-symbol control for `§`
- Capital-letter blocking option
- Character-count limiter from `1` to `1000`
- Full chat lock and unlock
- NoMention protection for selected players
- FilterWords system with anti-bypass normalization
- Blocks filtered words even when users try spaces, dots, dashes, or symbols between letters
- Mention sound notification when someone is mentioned using `@PlayerName`
- Bypass permission for trusted staff
- Clean architecture: Command, Listener, Manager, Util
- Configurable messages and prefix

## Requirements

| Requirement | Version |
|---|---|
| PocketMine-MP | API 5.0.0+ |
| PHP | 8.1+ recommended |
| External libraries | None required |

ECMD is embedded inside the plugin folder, so you do **not** need to install ECMD separately.

## Installation

1. Download or clone this repository.
2. Put the `ChatSystem` folder inside your server `plugins/` folder.
3. Start the server once to generate the data files.
4. Edit `plugin_data/ChatSystem/config.yml` if needed.
5. Restart the server.

To build a `.phar`, use DevTools or your preferred PMMP plugin build workflow.

## Commands

| Command | Description |
|---|---|
| `/chat MessageCooldown <1-60>` | Sets cooldown between chat messages and commands. |
| `/chat ColoringMessage <on\|off>` | Allows or blocks Minecraft color symbol `§` in chat. |
| `/chat CapitalLetters <on\|off>` | Allows or blocks capital letters in chat. |
| `/chat CharacterCount <1-1000>` | Sets the maximum allowed message length. |
| `/chat Lock` | Locks public chat. |
| `/chat Unlock` | Unlocks public chat. |
| `/chat noMention <add\|remove> <player>` | Adds or removes a player from NoMention protection. |
| `/chat ListNoMention` | Lists all protected NoMention players. |
| `/chat FilterWords <add\|remove> <word>` | Adds or removes a filtered word. |
| `/chat ListFilterWords` | Lists all filtered words. |

## Permissions

| Permission | Default | Description |
|---|---:|---|
| `chatsystem.admin` | OP | Allows using `/chat` management commands. |
| `chatsystem.bypass` | OP | Bypasses cooldown, lock, color, caps, length, NoMention, and filter restrictions. |

## Filter bypass handling

The filter compacts and normalizes the message before checking words.  
For example, if `badword` is filtered, all of these are blocked:

```text
badword
b.a.d.w.o.r.d
b a d w o r d
b-a-d-w-o-r-d
b_a_d_w_o_r_d
```

Players with `chatsystem.bypass` can bypass the filter.

## Project structure

```text
ChatSystem/
├── src/
│   ├── Aczj/ChatSystem/
│   │   ├── Command/
│   │   ├── Listener/
│   │   ├── Manager/
│   │   ├── Util/
│   │   └── ChatSystem.php
│   └── AEDXDEV/ECMD/
├── resources/
├── docs/
├── plugin.yml
├── composer.json
└── README.md
```

## Author

Discord: **aczj**
