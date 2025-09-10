'use client';

import { CreateToolForm } from '@/features/tools/components/CreateToolForm';

export default function CreateToolPage() {
  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-white shadow">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between items-center py-6">
            <div className="flex items-center">
              <h1 className="text-2xl font-bold text-gray-900">Dodaj narzędzie</h1>
            </div>
            <div className="flex items-center space-x-4">
              <a
                href="/tools"
                className="text-gray-600 hover:text-gray-900 text-sm font-medium"
              >
                ← Powrót do listy
              </a>
            </div>
          </div>
        </div>
      </header>

      {/* Main Content */}
      <main className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div className="px-4 py-6 sm:px-0">
          <CreateToolForm />
        </div>
      </main>
    </div>
  );
}
