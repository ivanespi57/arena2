import { Link } from 'react-router-dom'
import { Calendar, MapPin } from 'lucide-react'

export default function EventCard({ evento }) {
  const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('es-ES', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    })
  }

  const minPrice = evento.precios?.length > 0
    ? Math.min(...evento.precios.map(p => p.precio))
    : 0

  return (
    <div className="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
      {/* Imagen del evento */}
      <div className="relative h-48 bg-gray-200 overflow-hidden">
        {evento.imagen ? (
          <img
            src={evento.imagen}
            alt={evento.nombre}
            className="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
          />
        ) : (
          <div className="w-full h-full bg-gradient-to-br from-red-100 to-red-200 flex items-center justify-center">
            <span className="text-red-400">Sin imagen</span>
          </div>
        )}
      </div>

      {/* Contenido */}
      <div className="p-5 space-y-3">
        {/* Nombre del evento */}
        <h3 className="text-lg font-bold text-gray-900 line-clamp-2">
          {evento.nombre}
        </h3>

        {/* Descripción */}
        {evento.descripcion && (
          <p className="text-gray-600 text-sm line-clamp-2">
            {evento.descripcion}
          </p>
        )}

        {/* Fecha */}
        <div className="flex items-center gap-2 text-gray-700 text-sm">
          <Calendar className="w-4 h-4 text-red-600" />
          <span>{formatDate(evento.fecha)}</span>
        </div>

        {/* Ubicación */}
        {evento.ubicacion && (
          <div className="flex items-center gap-2 text-gray-700 text-sm">
            <MapPin className="w-4 h-4 text-red-600" />
            <span>{evento.ubicacion}</span>
          </div>
        )}

        {/* Precio destacado */}
        <div className="pt-2 border-t border-gray-200">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-gray-600 text-sm">Desde</p>
              <p className="text-2xl font-bold text-red-600">
                ${minPrice.toFixed(2)}
              </p>
            </div>

            {/* Botón */}
            <Link
              to={`/evento/${evento.id}`}
              className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition"
            >
              Ver más y comprar
            </Link>
          </div>
        </div>
      </div>
    </div>
  )
}
