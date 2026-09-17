# Smart Contract Flow

WordPress plugin that connects a page to an EVM smart contract. Visitors connect a wallet, pick a quantity, and mint an NFT. Admins read and write contract functions from the WordPress dashboard.

Built for Polygon (Mainnet and Mumbai testnet). Version 0.0.1.

## Features

- Wallet connection with WalletConnect v2 (Web3Modal + wagmi).
- Mint button that calls `mintTo(quantity, address)` and pays `mintPrice * quantity` in MATIC.
- Live supply counter and fiat price display (MATIC price from CoinGecko).
- Crossmint pay button for credit card checkout.
- Admin tab generated from the contract ABI: trigger any `view`, `nonpayable`, or `payable` function.
- Translations: `de_DE`, `de_CH`.

## Requirements

- WordPress with an administrator account.
- A deployed ERC-721 contract on Polygon with `mintTo`, `mintPrice`, `maxQuantity`, `maxSupply`, and `totalSupply`.
- A WalletConnect project ID.
- An Alchemy API key.
- Optional: a Crossmint project and collection ID.

## Install

1. Copy this folder to `wp-content/plugins/sc-flow`.
2. Activate **Smart Contract Flow** in the WordPress plugins list.
3. Open **Smart Contract Flow** in the admin sidebar.

## Configure

The settings page has four tabs.

**General Settings**

| Field | Meaning |
|---|---|
| Contract Address | Address of the deployed contract. |
| Page Slugs | Comma separated slugs of the pages where scripts load, for example `mint, shop`. |
| Smart Contract ABI | Contract ABI as JSON. Drives the Admin Operations tab. |
| Active Chain | `137` Polygon Mainnet or `80001` Mumbai. |
| Wallet connect Project ID | From cloud.walletconnect.com. |
| Alchemy provider project ID | From alchemy.com. |
| Fiat Currency | ISO code for price display, default `chf`. |

To keep API keys out of the database, define them in `wp-config.php` instead. A defined constant wins over the saved option.

```php
define('SC_FLOW_WALLETCONNECT_PROJECT_ID', '...');
define('SC_FLOW_ALCHEMY_API_KEY', '...');
```

**Admin Operations**

Lists every function from the ABI grouped by state mutability. Connect the owner wallet with the button at the top, fill in inputs, and click **Trigger**. Read results are saved as options and passed to the frontend as `maxSupply`, `mintPrice`, `maxQuantity`, and `totalSupply`.

**Crossmint configuration**

Project ID, collection ID, environment (`staging` or `production`), and ERC type.

**Buttons attributes**

CSS selectors the frontend script binds to. Defaults are set on activation.

| Setting | Default |
|---|---|
| Connect button | `.connect-wallet-button` |
| Mint button | `.mint-btn-one` |
| Mint quantity input | `.nft-quantity` |
| Minter counter | `#left-to-mint` |

## Shortcodes

Use these on any page listed in **Page Slugs**.

```
[connect_wallet]
[mint_button class="mint-btn-one" id="wallet-mint-btn"]
[crossmint_payment_button]
```

The mint flow also expects these elements on the page:

- A quantity input matching the **Mint quantity** selector.
- `.increase-btn` and `.decrease-btn` buttons.
- A `.terms-checkbox` checkbox.
- Optional `.final-nft-price`, `.final-nft-price-crypto`, and `#total-nft-sup` text targets.

## Project layout

```
sc-flow.php                         Plugin entry, activation hook, wallet module enqueue
includes/constants.php              Option names and slugs
includes/class-sc-flow-settings.php Settings payload passed to JavaScript
includes/class-sc-flow-public.php   Frontend enqueue and shortcodes
includes/admin/                     Settings page and its four tabs
assets/js/connect-wallet-wagmi.js   Web3Modal + wagmi setup (ES module)
assets/js/sc-flow-read.js           Contract read and mint calls
assets/js/sc-flow-frontend.js       Mint UI logic
assets/js/sc-flow-write-admin.js    Admin Operations trigger logic
assets/css/                         Admin and frontend styles
languages/                          .po and .mo translation files
```

## Development

No build step. Edit files in place and reload the page.

Lint PHP against the WordPress coding standards:

```
composer install
composer lint
```

External scripts load from CDNs at runtime: ethers 6.5.1, `@web3modal/ethereum` 2.7.1, `@web3modal/html` 2.7.1, and `@crossmint/client-sdk-vanilla-ui` 1.0.1-alpha.6. WordPress.org does not allow this for hosted plugins; bundle them under `assets/vendor/` before submitting there.

## License

Proprietary. Copyright 2023 Bicatalyst. Contact info@bicatalyst.com for licensing.
