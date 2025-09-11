'use client';

import { useToolsQuery } from '../api/useToolsQuery';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
import { useEffect } from 'react';

export function ToolsList() {
  const { data, isLoading, isError, error } = useToolsQuery();
  const router = useRouter();

  // Handle authentication error
  useEffect(() => {
    if (isError && error?.message === 'No access token available') {
      router.push('/login');
    }
  }, [isError, error, router]);

  if (isLoading) {
    return (
      <div className="flex justify-center items-center py-8">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  if (isError) {
    if (error?.message === 'No access token available') {
      return (
        <div className="bg-yellow-50 border border-yellow-200 rounded-md p-4">
          <p className="text-yellow-600">Przekierowywanie do logowania...</p>
        </div>
      );
    }
    
    return (
      <div className="bg-red-50 border border-red-200 rounded-md p-4">
        <p className="text-red-600">Wystąpił błąd podczas ładowania narzędzi.</p>
        <p className="text-sm text-red-500 mt-2">{error?.message}</p>
      </div>
    );
  }

  if (!data?.length) {
    return (
      <div className="text-center py-8">
        <p className="text-gray-500 mb-4">Brak narzędzi w systemie.</p>
        <Link
          href="/tools/create"
          className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
          Dodaj pierwsze narzędzie
        </Link>
      </div>
    );
  }

  return (
    <div className="bg-white shadow overflow-hidden sm:rounded-md">
      <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 className="text-lg leading-6 font-medium text-gray-900">
            Lista narzędzi
          </h3>
          <p className="mt-1 max-w-2xl text-sm text-gray-500">
            Zarządzaj narzędziami w systemie
          </p>
        </div>
        <Link
          href="/tools/create"
          className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
        >
          Dodaj narzędzie
        </Link>
      </div>
      <ul className="divide-y divide-gray-200">
        {data.map((tool) => (
          <li key={tool.id}>
            <div className="px-4 py-4 sm:px-6">
              <div className="flex items-center justify-between">
                <div className="flex-1">
                  <div className="flex items-center justify-between">
                    <p className="text-sm font-medium text-blue-600 truncate">
                      {tool.name}
                    </p>
                    <div className="ml-2 flex-shrink-0 flex">
                      <p className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        {tool.count} szt.
                      </p>
                    </div>
                  </div>
                  <div className="mt-2">
                    <p className="text-sm text-gray-500">{tool.description}</p>
                  </div>
                </div>
                <div className="ml-4 flex-shrink-0 flex space-x-2">
                  <Link
                    href={`/tools/${tool.id}`}
                    className="text-blue-600 hover:text-blue-900 text-sm font-medium"
                  >
                    Zobacz
                  </Link>
                  <Link
                    href={`/tools/${tool.id}/edit`}
                    className="text-gray-600 hover:text-gray-900 text-sm font-medium"
                  >
                    Edytuj
                  </Link>
                </div>
              </div>
            </div>
          </li>
        ))}
      </ul>
    </div>
  );
}
