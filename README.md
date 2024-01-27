# Leveling System Plugin

## Overview

The Leveling System Plugin is a custom plugin for PocketMine-MP that introduces a flexible and customizable leveling system for players on your Minecraft server. Players can progress through levels, and receive rewards as they achieve higher levels.

## Features

- **Levels:** Each level comes with its own set of messages, money requirements, and rewards.
- **Customizable Rewards:** Specify rewards for each level, including items, custom names, lore, and enchantments.
- **Sound Effects:** Play custom sounds on level-up events to enhance the player experience.
- **Localization Support:** Choose the language setting for messages (e.g., English, default).

## Configuration

```yaml
# DO NOT TOUCH - Config Version
version: 1.0

# Global settings
settings:
  lang: "ENG-def"
  sound: "random.levelup"
  
# Level-specific configurations
levels:
  1:
    messages:
      - "     &r&l&f<&6Level UP: 1&7>"
      - "     &r&l&1Level Progression 1 &f/10"
      - " "
      - "&r&l&aYou need $1,000 to go to the next level."
    price: 500
    rewards:
      - item: "diamond"
        amount: 1
      - item: "beacon"
        amount: 1
        name: "&r&l&fLootbox: &aTest"
        lore:
          - "&r&7Line one"
          - "&r&7Line two"
        nbt:
          tag: "lootbox"
          value: "test"
  2:
    messages:
      - "     &r&l&f<&6Level UP: 2&7>"
      - "     &r&l&1Level Progression 2 &f/ 10"
      - " "
      - "&r&l&aYou need $1,500 to go to the next level."
    price: 1000
    rewards:
      - item: "diamond"
        amount: 1
      - item: "diamond_sword"
        amount: 1
        name: "&r&l&fLevel 2 sword"
        lore:
          - "&r&7Line one"
          - "&r&7Line two"
        enchantments:
          - enchant: "unbreaking"
            level: 2
