# Commands

All management commands use the main `/chat` command.

## MessageCooldown

```text
/chat MessageCooldown <seconds>
```

Sets the cooldown between any chat message or command. The value must be from `1` to `60`.

## ColoringMessage

```text
/chat ColoringMessage <on|off>
```

- `on`: players can use the Minecraft color symbol `§` in chat.
- `off`: messages containing `§` are cancelled.

Players with `chatsystem.bypass` are not blocked.

## CapitalLetters

```text
/chat CapitalLetters <on|off>
```

- `on`: capital letters are allowed.
- `off`: messages containing uppercase letters are cancelled.

Players with `chatsystem.bypass` are not blocked.

## CharacterCount

```text
/chat CharacterCount <count>
```

Sets the maximum chat message length. The value must be from `1` to `1000`.

Players with `chatsystem.bypass` can send longer messages.

## Lock / Unlock

```text
/chat Lock
/chat Unlock
```

Locks or unlocks public chat. When chat is locked, only players with `chatsystem.bypass` can chat.

## NoMention

```text
/chat noMention <add|remove> <player>
/chat ListNoMention
```

When a player is protected by NoMention, normal players cannot mention them using `@Name`.

Players with `chatsystem.bypass` can still mention protected players.

## FilterWords

```text
/chat FilterWords <add|remove> <word>
/chat ListFilterWords
```

Adds or removes blocked words. Filtered words block the whole message.

The filter tries to catch bypass attempts such as dots, spaces, dashes, underscores, and symbols between letters.
