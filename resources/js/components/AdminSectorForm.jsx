import { useState, useEffect } from 'react'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'
import apiClient from '@/api/client'
import { Loader } from 'lucide-react'

const sectorSchema = z.object({
  nombre: z.string().min(1, 'El nombre es requerido'),
  evento_id: z.string().min(1, 'Selecciona un evento'),
  asientos_cantidad: z.number().min(1, 'Debe haber al menos 1 asiento'),
})

export default function AdminSectorForm({ onSuccess, onCancel, editingId }) {
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState('')
  const [eventos, setEventos] = useState([])

  const { register, handleSubmit, formState: { errors }, reset, setValue } = useForm({
    resolver: zodResolver(sectorSchema),
  })

  useEffect(() => {
    const loadEventos = async () => {
      try {
        const response = await apiClient.get('/admin/eventos')
        setEventos(response.data)
      } catch (err) {
        console.error('Error al cargar eventos', err)
      }
    }
    loadEventos()

    if (editingId) {
      const loadSector = async () => {
        try {
          const response = await apiClient.get(`/admin/sectores/${editingId}`)
          setValue('nombre', response.data.nombre)
          setValue('evento_id', response.data.evento_id)
          setValue('asientos_cantidad', response.data.asientos_cantidad)
        } catch (err) {
          setError('Error al cargar el sector')
        }
      }
      loadSector()
    }
  }, [editingId, setValue])

  const onSubmit = async (data) => {
    setIsLoading(true)
    setError('')

    try {
      if (editingId) {
        await apiClient.put(`/admin/sectores/${editingId}`, data)
      } else {
        await apiClient.post('/admin/sectores', data)
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
        {editingId ? 'Editar Sector' : 'Nuevo Sector'}
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
          placeholder="Nombre del sector"
        />
        {errors.nombre && <p className="text-red-600 text-xs mt-1">{errors.nombre.message}</p>}
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">
          Evento *
        </label>
        <select
          {...register('evento_id')}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 outline-none"
        >
          <option value="">Selecciona un evento</option>
          {eventos.map(evento => (
            <option key={evento.id} value={evento.id}>
              {evento.nombre}
            </option>
          ))}
        </select>
        {errors.evento_id && <p className="text-red-600 text-xs mt-1">{errors.evento_id.message}</p>}
      </div>

      <div>
        <label className="block text-sm font-medium text-gray-700 mb-1">
          Cantidad de Asientos *
        </label>
        <input
          type="number"
          {...register('asientos_cantidad', { valueAsNumber: true })}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-600 outline-none"
          placeholder="50"
          min="1"
        />
        {errors.asientos_cantidad && <p className="text-red-600 text-xs mt-1">{errors.asientos_cantidad.message}</p>}
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
