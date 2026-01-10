import { isPaywallEnabled as isPaywallEnabledConfig } from './config';

enum EconumoEdition {
  COMMUNITY_EDITION = 'ce',
  CLOUD = 'cloud',
}

interface EconumoPackage {
  edition: EconumoEdition;
  label: string;
  includesConnections: boolean;
  includesSharedAccess: boolean;
  isPaywallEnabled: boolean;
  paywallUrl: string;
}

function getEdition(): EconumoEdition {
  if (process.env.ECONUMO_EDITION === EconumoEdition.CLOUD) {
    return EconumoEdition.CLOUD;
  }

  return EconumoEdition.COMMUNITY_EDITION;
}

function getEditionLabel(): string {
  return String(process.env.ECONUMO_EDITION_LABEL);
}

function isPackageIncludesConnections(): boolean {
  return process.env.ECONUMO_EDITION === EconumoEdition.CLOUD;
}

function isPackageIncludesSharedAccess(): boolean {
  return true;
}

function isPaywallEnabled(): boolean {
  return process.env.ECONUMO_EDITION === EconumoEdition.CLOUD && isPaywallEnabledConfig();
}

function getPaywallUrl(): string {
  if (isPaywallEnabled()) {
    switch (getEdition()) {
      case EconumoEdition.CLOUD:
        return 'https://pay.econumo.com/cloud/';
    }
  }
  return '';
}

export const econumoPackage: EconumoPackage = {
  edition: getEdition(),
  label: getEditionLabel(),
  includesConnections: isPackageIncludesConnections(),
  includesSharedAccess: isPackageIncludesSharedAccess(),
  isPaywallEnabled: isPaywallEnabled(),
  paywallUrl: getPaywallUrl(),
};
