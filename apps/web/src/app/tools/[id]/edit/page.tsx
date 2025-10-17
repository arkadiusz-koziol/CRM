import { EditToolForm } from '@/features/tools/components/EditToolForm';

interface EditToolPageProps {
  params: {
    id: string;
  };
}

export default async function EditToolPage({ params }: EditToolPageProps) {
  const { id } = await params;
  const toolId = parseInt(id, 10);

  if (isNaN(toolId)) {
    return (
      <div className="min-h-screen bg-gray-50 py-8">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center py-16">
            <div className="mx-auto w-24 h-24 bg-gradient-to-br from-red-100 to-pink-100 rounded-full flex items-center justify-center mb-6">
              <svg className="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <h3 className="text-xl font-semibold text-gray-900 mb-2">Invalid Tool ID</h3>
            <p className="text-gray-500 mb-8 max-w-sm mx-auto">The tool ID must be a valid number.</p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <EditToolForm toolId={toolId} />
      </div>
    </div>
  );
}
