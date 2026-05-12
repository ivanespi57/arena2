import { useQuery } from '@tanstack/react-query'
import { Link } from 'react-router-dom'
import apiClient from '@/api/client'
import { Loader } from 'lucide-react'
import EventCard from '@/components/EventCard'

function fetchEventos() {
  return apiClient.get('/eventos').then(res => res.data)
}

export default function EventosPage() {
  const { data: eventos, isLoading, error } = useQuery({
    queryKey: ['eventos'],
    queryFn: fetchEventos,
  })

  if (isLoading) {
    return (
      <div className="flex justify-center items-center min-h-[60vh]">
        <Loader className="w-8 h-8 animate-spin text-red-600" />
      </div>
    )
  }

  if (error) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
        <p className="text-red-700">Error al cargar los eventos</p>
      </div>
    )
  }

  return (
    <div className="space-y-8">
      <div className="text-center">
        <h1 className="text-4xl font-bold text-gray-900">Eventos Disponibles</h1>
        <p className="text-gray-600 mt-2">Descubre y compra entradas para nuestros eventos</p>
      </div>

      {eventos && eventos.length > 0 ? (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {eventos.map((evento) => (
            <EventCard key={evento.id} evento={evento} />
          ))}
        </div>
      ) : (
        <div className="text-center py-12">
          <p className="text-gray-600 text-lg">No hay eventos disponibles en este momento</p>
        </div>
      )}
    </div>
  )
}
