'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
import { useCreateTool } from '../api/useCreateTool';
import { CreateToolRequest } from '../api/useCreateTool';

export function CreateToolForm() {
  const router = useRouter();
  const createTool = useCreateTool();
  const [formData, setFormData] = useState<CreateToolRequest>({
    name: '',
    description: '',
    count: 0,
  });
  const [errors, setErrors] = useState<Record<string, string>>({});

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setErrors({});

    try {
      await createTool.mutateAsync(formData);
      router.push('/tools');
    } catch (error: any) {
      if (error.details?.errors) {
        setErrors(error.details.errors);
      } else {
        setErrors({ general: 'Wystąpił błąd podczas tworzenia narzędzia.' });
      }
    }
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: name === 'count' ? parseInt(value) || 0 : value,
    }));
  };

  return (
    <div className="max-w-2xl mx-auto">
      <div className="bg-white shadow sm:rounded-lg">
        <div className="px-4 py-5 sm:p-6">
          <h3 className="text-lg leading-6 font-medium text-gray-900 mb-6">
            Dodaj nowe narzędzie
          </h3>
          
          {errors.general && (
            <div className="mb-4 bg-red-50 border border-red-200 rounded-md p-4">
              <p className="text-red-600">{errors.general}</p>
            </div>
          )}

          <form onSubmit={handleSubmit} className="space-y-6">
            <div>
              <label htmlFor="name" className="block text-sm font-medium text-gray-700">
                Nazwa narzędzia *
              </label>
              <div className="mt-1">
                <input
                  type="text"
                  name="name"
                  id="name"
                  value={formData.name}
                  onChange={handleChange}
                  className={`shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border rounded-md ${
                    errors.name ? 'border-red-300' : 'border-gray-300'
                  }`}
                  placeholder="Np. Młotek"
                />
                {errors.name && (
                  <p className="mt-2 text-sm text-red-600">{errors.name}</p>
                )}
              </div>
            </div>

            <div>
              <label htmlFor="description" className="block text-sm font-medium text-gray-700">
                Opis *
              </label>
              <div className="mt-1">
                <textarea
                  name="description"
                  id="description"
                  rows={3}
                  value={formData.description}
                  onChange={handleChange}
                  className={`shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border rounded-md ${
                    errors.description ? 'border-red-300' : 'border-gray-300'
                  }`}
                  placeholder="Opis narzędzia, np. materiał, rozmiar, zastosowanie"
                />
                {errors.description && (
                  <p className="mt-2 text-sm text-red-600">{errors.description}</p>
                )}
              </div>
            </div>

            <div>
              <label htmlFor="count" className="block text-sm font-medium text-gray-700">
                Liczba sztuk *
              </label>
              <div className="mt-1">
                <input
                  type="number"
                  name="count"
                  id="count"
                  min="0"
                  value={formData.count}
                  onChange={handleChange}
                  className={`shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border rounded-md ${
                    errors.count ? 'border-red-300' : 'border-gray-300'
                  }`}
                  placeholder="0"
                />
                {errors.count && (
                  <p className="mt-2 text-sm text-red-600">{errors.count}</p>
                )}
              </div>
            </div>

            <div className="flex justify-end space-x-3">
              <button
                type="button"
                onClick={() => router.push('/tools')}
                className="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50"
              >
                Anuluj
              </button>
              <button
                type="submit"
                disabled={createTool.isPending}
                className="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              >
                {createTool.isPending ? 'Zapisywanie...' : 'Zapisz'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
