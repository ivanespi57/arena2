import { useState, useEffect } from 'react'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import apiClient from '@/api/client'
import { Loader } from 'lucide-react'

const eventoSchema = z.object({
  nombre: z.string().min(1, 'El nombre es requerido'),
  descripcion: z.string().optional(),
  fecha: z.string().min(1, 'La fecha es requerida'),
  ubicacion: z.string().optional(),
  imagen: z.string().optional(),
})

export default function AdminEventForm({ onSuccess, onCancel, editingId }) {
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState('')
  const [evento, setEvento] = useState(null)

  const { register, handleSubmit, formState: { errors }, reset, setValue } = useForm({
    resolver: zodResolver(eventoSchema),
  })

  useEffect(() => {
    if (editingId) {
      const loadEvento = async () => {
        try {
          const response = await apiClient.get(`/admin/eventos/${editingId}`)
          setEvento(response.data)
          Object.keys(response.data).forEach(key => {
            if (key === 'fecha') {
              setValue(key, response.data[key].split('T')[0])
            } else {
              setValue(key, response.data[key])
            }
          })
        } catch (err) {
          setError('Error al cargar el evento')
        }
      }
      loadEvento()
    }
  }, [editingId, setValue])

  const onSubmit = async (data) => {
    setIsLoading(true)
    setError('')

    try {
      if (editingId) {
        await apiClient.put(`/admin/eventos/${editingId}`, data)
      } else {
        await apiClient.post('/admin/eventos', data)
      }
      reset()
      onSuccess()
    } catch (err) {
      setError(err.response?.data?.message || 'Error al guardar')
    } finally {
      setIsLoading(false)
    }
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="space-y-4">
      <h3 className="text-xl font-bold text-gray-900 mb-4">
        {editingId ? 'Editar Evento' : 'Nuevo Evento'}
      </h3>

      {error && (
        <div className="bg-red-50 border border-red-200 rounded p-3 text-red-700 text-sm">
          {error}
        </div>
      )}

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">
          Nombre *
        </label>
        <input
          {...register('nombre')}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 outline-none"
          placeholder="Nombre del evento"
        />
        {errors.nombre && <p className="text-red-600 text-xs mt-1">{errors.nombre.message}</p>}
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">
          Descripción
        </label>
        <textarea
          {...register('descripcion')}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 outline-none"
          placeholder="Descripción del evento"
          rows="3"
        />
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">
          Fecha *
        </label>
        <input
          type="date"
          {...register('fecha')}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 outline-none"
        />
        {errors.fecha && <p className="text-red-600 text-xs mt-1">{errors.fecha.message}</p>}
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">
          Ubicación
        </label>
        <input
          {...register('ubicacion')}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 outline-none"
          placeholder="Ubicación del evento"
        />
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">
          URL de Imagen
        </label>
        <input
          {...register('imagen')}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 outline-none"
          placeholder="https://..."
        />
      </div>

      <div className="flex gap-2">
        <button
          type="submit"
          disabled={isLoading}
          className="flex-1 bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white py-2 rounded-lg transition flex items-center justify-center gap-2"
        >
          {isLoading && <Loader className="w-4 h-4 animate-spin" />}
          {isLoading ? 'Guardando...' : 'Guardar'}
        </button>
        <button
          type="button"
          onClick={onCancel}
          className="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg hover:bg-gray-50 transition"
        >
          Cancelar
        </button>
      </div>
    </form>
  )
}
