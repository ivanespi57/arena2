import { Edit2, Trash2, Loader } from 'lucide-react'
import apiClient from '@/api/client'
import { useState } from 'react'

export default function AdminSectorList({ sectores, onEdit, onDeleteSuccess }) {
  const [deleting, setDeleting] = useState(null)

  const handleDelete = async (id) => {
    if (!confirm('¿Estás seguro que deseas eliminar este sector?')) return

    setDeleting(id)
    try {
      await apiClient.delete(`/admin/sectores/${id}`)
      onDeleteSuccess()
    } catch (error) {
      alert('Error al eliminar: ' + error.response?.data?.message)
    } finally {
      setDeleting(null)
    }
  }

  if (sectores.length === 0) {
    return (
      <div className="bg-white rounded-lg shadow p-8 text-center text-gray-600">
        No hay sectores aún
      </div>
    )
  }

  return (
    <div className="bg-white rounded-lg shadow-lg overflow-hidden">
      <table className="w-full">
        <thead className="bg-gray-50 border-b">
          <tr>
            <th className="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nombre</th>
            <th className="px-6 py-3 text-left text-sm font-semibold text-gray-900">Evento</th>
            <th className="px-6 py-3 text-left text-sm font-semibold text-gray-900">Asientos</th>
            <th className="px-6 py-3 text-left text-sm font-semibold text-gray-900">Acciones</th>
          </tr>
        </thead>
        <tbody className="divide-y">
          {sectores.map((sector) => (
            <tr key={sector.id} className="hover:bg-gray-50">
              <td className="px-6 py-4">
                <span className="font-medium text-gray-900">{sector.nombre}</span>
              </td>
              <td className="px-6 py-4 text-gray-600">{sector.evento?.nombre}</td>
              <td className="px-6 py-4 text-gray-600">{sector.asientos_cantidad}</td>
              <td className="px-6 py-4">
                <div className="flex gap-2">
                  <button
                    onClick={() => onEdit(sector.id)}
                    className="text-blue-600 hover:text-blue-700 p-2"
                  >
                    <Edit2 className="w-4 h-4" />
                  </button>
                  <button
                    onClick={() => handleDelete(sector.id)}
                    disabled={deleting === sector.id}
                    className="text-red-600 hover:text-red-700 p-2 disabled:opacity-50"
                  >
                    {deleting === sector.id ? (
                      <Loader className="w-4 h-4 animate-spin" />
                    ) : (
                      <Trash2 className="w-4 h-4" />
                    )}
                  </button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  )
}
