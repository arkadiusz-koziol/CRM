'use client';

import { useEstatesQuery } from '../api/useEstatesQuery';
import Link from 'next/link';

export function EstatesList() {
  const { data, isLoading, isError } = useEstatesQuery();

  if (isLoading) {
    return (
      <div className="flex justify-center items-center py-8">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  if (isError) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-md p-4">
        <p className="text-red-600">Wystąpił błąd podczas ładowania nieruchomości.</p>
      </div>
    );
  }

  if (!data?.data?.length) {
    return (
      <div className="text-center py-8">
        <p className="text-gray-500 mb-4">Brak nieruchomości w systemie.</p>
        <Link
          href="/estates/create"
          className="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700"
        >
          Dodaj pierwszą nieruchomość
        </Link>
      </div>
    );
  }

  return (
    <div className="bg-white shadow overflow-hidden sm:rounded-md">
      <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 className="text-lg leading-6 font-medium text-gray-900">
            Lista nieruchomości
          </h3>
          <p className="mt-1 max-w-2xl text-sm text-gray-500">
            Zarządzaj nieruchomościami w systemie
          </p>
        </div>
        <Link
          href="/estates/create"
          className="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700"
        >
          Dodaj nieruchomość
        </Link>
      </div>
      <ul className="divide-y divide-gray-200">
        {data.data.map((estate) => (
          <li key={estate.id}>
            <div className="px-4 py-4 sm:px-6">
              <div className="flex items-center justify-between">
                <div className="flex-1">
                  <div className="flex items-center justify-between">
                    <p className="text-sm font-medium text-purple-600 truncate">
                      {estate.name}
                    </p>
                    <div className="ml-2 flex-shrink-0 flex">
                      <p className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                        {estate.custom_id}
                      </p>
                    </div>
                  </div>
                  <div className="mt-2">
                    <p className="text-sm text-gray-500">
                      {estate.street} {estate.house_number}, {estate.postal_code}
                    </p>
                  </div>
                </div>
                <div className="ml-4 flex-shrink-0 flex space-x-2">
                  <Link
                    href={`/estates/${estate.id}`}
                    className="text-purple-600 hover:text-purple-900 text-sm font-medium"
                  >
                    Zobacz
                  </Link>
                  <Link
                    href={`/estates/${estate.id}/edit`}
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
