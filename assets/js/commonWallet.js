function getInjectedProvider(walletName, { timeout } = { timeout: 3000 }) {
  const provider = getWalletFromWindow(walletName);

  if (provider) {
    return provider;
  }

  return listenForWalletInitialized(walletName, { timeout });
}

function listenForWalletInitialized(
  walletName,
  { timeout } = { timeout: 3000 }
) {
  return new Promise((resolve) => {
    const handleInitialization = () => {
      resolve(getWalletFromWindow(walletName));
    };

    window.addEventListener(`${walletName}#initialized`, handleInitialization, {
      once: true,
    });

    setTimeout(() => {
      window.removeEventListener(
        `${walletName}#initialized`,
        handleInitialization,
        {
          once: true,
        }
      );
      resolve(null);
    }, timeout);
  });
}

function getWalletFromWindow(walletName) {
  // const isWallet = (ethereum) => {
  //   // Identify if the wallet injected provider is present.
  //   return !!ethereum[`is${walletName}`];
  // };
  function isWallet(ethereum, walletName) {
    // Identify if the wallet injected provider is present and matches the wallet name.
    if (walletName === "TrustWallet") {
      const trustWallet = !!ethereum.isTrust;
      return trustWallet;
    } else if (walletName === "MetaMask") {
      const metaWallet = !!ethereum.isMetaMask;
      return metaWallet;
    } else if (walletName === "CoinbaseWallet") {
      const coinbaseWallet = !!ethereum.isCoinbase;
      return coinbaseWallet;
    }
  }

  const injectedProviderExist =
    typeof window !== "undefined" && typeof window.ethereum !== "undefined";

  // No injected providers exist.
  if (!injectedProviderExist) {
    return null;
  }

  // // The wallet was injected into window.ethereum.
  // if (isWallet(window.ethereum, walletName)) {
  //   return window.ethereum;
  // }

  // The wallet provider might be replaced by another injected provider, check the providers array.
  if (window.ethereum.providers) {
    const _provider = window.ethereum.providers.find(function (provider) {
      return provider.isMetaMask ? provider : false;
    });
    // ethereum.providers array is a non-standard way to preserve multiple injected providers.
    // Eventually, EIP-5749 will become a living standard, and we will have to update this.
    return _provider ? _provider : null;
  }
  // The wallet was injected into window.ethereum.
  if (window.ethereum && isWallet(window.ethereum, walletName)) {
    return window.ethereum;
  }

  // The wallet injected provider is available in the global scope.
  // There are cases where injected providers can replace window.ethereum without updating the ethereum.providers array.
  // To prevent issues where the connector does not recognize the provider when the extension is installed,
  // we begin our checks by relying on the wallet's global object.
  return window[walletName.toLowerCase()] ?? null;
}

function getWalletInjectedProvider(
  { wallet, timeout } = { wallet: "MetaMask", timeout: 3000 }
) {
  return getInjectedProvider(wallet, { timeout });
}

// example usage
// return metamask wallet provider
// getWalletInjectedProvider()
// return trust wallet provider
// getWalletInjectedProvider({ wallet: "TrustWallet", timeout: 3000 })
