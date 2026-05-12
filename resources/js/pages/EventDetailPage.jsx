import { useState } from 'react'
import { useParams, useNavigate } from 'react-router-dom'
import { useQuery } from '@tanstack/react-query'
import apiClient from '@/api/client'
import SeatMap from '@/components/SeatMap'
import { Loader, AlertCircle } from 'lucide-react'

function fetchEvento(id) {
  return apiClient.get(`/eventos/${id}`).then(res => res.data)
}

export default function EventDetailPage() {
  const { id } = useParams()
  const navigate = useNavigate()
  const [selectedSeats, setSelectedSeats] = useState([])
  const [selectedPrice, setSelectedPrice] = useState(null)

  const { data: evento, isLoading, error } = useQuery({
    queryKey: ['evento', id],
    queryFn: () => fetchEvento(id),
  })

  if (isLoading) {
    return (
      <div className="flex justify-center items-center min-h-[60vh]">
        <Loader className="w-8 h-8 animate-spin text-red-600" />
      </div>
    )
  }

  if (error || !evento) {
    return (
      <div className="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
        <AlertCircle className="w-8 h-8 text-red-600 mx-auto mb-2" />
        <p className="text-red-700">Error al cargar el evento</p>
      </div>
    )
  }

  const handleAddToCart = () => {
    if (!selectedSeats.length || !selectedPrice) {
      alert('Por favor selecciona asientos y una categoría de precio')
      return
    }

    // Aquí irá la lógica para agregar al carrito
    console.log('Agregando al carrito:', { evento: evento.id, seats: selectedSeats, price: selectedPrice })
    navigate('/carrito')
  }

  return (
    <div className="space-y-8">
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Información del evento */}
        <div className="lg:col-span-1 bg-white rounded-lg shadow-lg p-6">
          <h1 className="text-3xl font-bold text-gray-900 mb-4">
            {evento.nombre}
          </h1>

          {evento.imagen && (
            <img
              src={evento.imagen}
              alt={evento.nombre}
              className="w-full h-auto rounded-lg mb-4"
            />
          )}

          <div className="space-y-3 text-gray-700">
            <p><strong>Fecha:</strong> {new Date(evento.fecha).toLocaleDateString('es-ES')}</p>
            {evento.ubicacion && (
              <p><strong>Ubicación:</strong> {evento.ubicacion}</p>
            )}
            {evento.descripcion && (
              <p className="mt-4">{evento.descripcion}</p>
            )}
          </div>

          {/* Categorías de precios */}
          {evento.precios && evento.precios.length > 0 && (
            <div className="mt-6 space-y-2 border-t pt-4">
              <h3 className="font-bold text-gray-900">Categorías de Precios</h3>
              {evento.precios.map((precio) => (
                <button
                  key={precio.id}
                  onClick={() => setSelectedPrice(precio)}
                  className={`w-full p-3 rounded-lg border-2 transition ${
                    selectedPrice?.id === precio.id
                      ? 'border-red-600 bg-red-50'
                      : 'border-gray-200 hover:border-red-300'
                  }`}
                >
                  <div className="flex justify-between">
                    <span className="font-medium">{precio.nombre}</span>
                    <span className="text-red-600 font-bold">${precio.precio.toFixed(2)}</span>
                  </div>
                </button>
              ))}
            </div>
          )}
        </div>

        {/* Mapa de asientos */}
        <div className="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
          <h2 className="text-2xl font-bold text-gray-900 mb-6">Selecciona tus Asientos</h2>
          <SeatMap evento={evento} onSeatsChange={setSelectedSeats} />

          {selectedSeats.length > 0 && (
            <div className="mt-6 p-4 bg-gray-50 rounded-lg">
              <p className="text-gray-700 mb-4">
                <strong>Asientos seleccionados ({selectedSeats.length}):</strong> {selectedSeats.join(', ')}
              </p>
              <button
                onClick={handleAddToCart}
                className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition"
              >
                Agregar al Carrito
              </button>
            </div>
          )}
        </div>
      </div>
    </div>
  )
}
