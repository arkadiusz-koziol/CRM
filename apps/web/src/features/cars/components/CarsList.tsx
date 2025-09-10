'use client';

import { useCarsQuery } from '../api/useCarsQuery';
import Link from 'next/link';

export function CarsList() {
  const { data, isLoading, isError } = useCarsQuery();

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
        <p className="text-red-600">Wystąpił błąd podczas ładowania pojazdów.</p>
      </div>
    );
  }

  if (!data?.data?.length) {
    return (
      <div className="text-center py-8">
        <p className="text-gray-500 mb-4">Brak pojazdów w systemie.</p>
        <Link
          href="/cars/create"
          className="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
        >
          Dodaj pierwszy pojazd
        </Link>
      </div>
    );
  }

  return (
    <div className="bg-white shadow overflow-hidden sm:rounded-md">
      <div className="px-4 py-5 sm:px-6 flex justify-between items-center">
        <div>
          <h3 className="text-lg leading-6 font-medium text-gray-900">
            Lista pojazdów
          </h3>
          <p className="mt-1 max-w-2xl text-sm text-gray-500">
            Zarządzaj pojazdami w systemie
          </p>
        </div>
        <Link
          href="/cars/create"
          className="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700"
        >
          Dodaj pojazd
        </Link>
      </div>
      <ul className="divide-y divide-gray-200">
        {data.data.map((car) => (
          <li key={car.id}>
            <div className="px-4 py-4 sm:px-6">
              <div className="flex items-center justify-between">
                <div className="flex-1">
                  <div className="flex items-center justify-between">
                    <p className="text-sm font-medium text-yellow-600 truncate">
                      {car.name}
                    </p>
                    <div className="ml-2 flex-shrink-0 flex">
                      <p className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        {car.registration_number}
                      </p>
                    </div>
                  </div>
                  <div className="mt-2">
                    <p className="text-sm text-gray-500">{car.description}</p>
                    {car.technical_details && (
                      <p className="text-xs text-gray-400 mt-1">{car.technical_details}</p>
                    )}
                  </div>
                </div>
                <div className="ml-4 flex-shrink-0 flex space-x-2">
                  <Link
                    href={`/cars/${car.id}`}
                    className="text-yellow-600 hover:text-yellow-900 text-sm font-medium"
                  >
                    Zobacz
                  </Link>
                  <Link
                    href={`/cars/${car.id}/edit`}
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
