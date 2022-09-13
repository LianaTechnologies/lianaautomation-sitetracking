# LianaAutomation Plugin for WordPress

This plugin provides necessary functions to integrate WordPress with LianaAutomation.

This plugin provides the following events: page browse

# Information for Developers:

## Project suggested linter for VSCode:

```
Name: phpcs
Id: ikappas.phpcs
Description: PHP CodeSniffer for Visual Studio Code
Version: 1.0.5
Publisher: Ioannis Kappas
VS Marketplace Link: https://marketplace.visualstudio.com/items?itemName=ikappas.phpcs
```

## Oneliner to create installable plugin from the repo directory

```
zip -r lianaautomation-version.zip lianaautomation -x "lianaautomation/.git*"
```

## How to install phpcs tools in macOS

```

#
# Install Homebrew tools, if you don't have them yet
#
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

#
# Install the PHP Composer via Homebrew
#
brew install composer

#
# Install phpcs globally using PHP Composer 
#
composer global require squizlabs/php_codesniffer

```

