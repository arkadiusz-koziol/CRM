import { useState, useEffect } from 'react';
import NetInfo, { NetInfoState } from '@react-native-community/netinfo';

export function useNetInfo(): {
  isConnected: boolean;
  isInternetReachable: boolean | null;
  type: string | null;
  isOffline: boolean;
} {
  const [netInfo, setNetInfo] = useState<NetInfoState>({
    isConnected: true,
    isInternetReachable: true,
    type: 'unknown',
  });

  useEffect(() => {
    const unsubscribe = NetInfo.addEventListener(state => {
      setNetInfo(state);
    });

    return () => unsubscribe();
  }, []);

  return {
    isConnected: netInfo.isConnected ?? false,
    isInternetReachable: netInfo.isInternetReachable,
    type: netInfo.type,
    isOffline: !netInfo.isConnected || netInfo.isInternetReachable === false,
  };
}
