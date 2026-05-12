import { useState, useEffect } from 'react'
import { useQuery } from '@tanstack/react-query'
import apiClient from '@/api/client'

function fetchAsientos(eventoId) {
  return apiClient.get(`/eventos/${eventoId}/asientos`).then(res => res.data)
}

export default function SeatMap({ evento, onSeatsChange }) {
  const [selectedSeats, setSelectedSeats] = useState([])
  const [selectedSector, setSelectedSector] = useState(null)

  const { data: asientos = [] } = useQuery({
    queryKey: ['asientos', evento.id],
    queryFn: () => fetchAsientos(evento.id),
  })

  // Agrupar asientos por sector
  const sectorMap = {}
  asientos.forEach(asiento => {
    const sectorName = asiento.sector || 'General'
    if (!sectorMap[sectorName]) {
      sectorMap[sectorName] = []
    }
    sectorMap[sectorName].push(asiento)
  })

  useEffect(() => {
    onSeatsChange(selectedSeats)
  }, [selectedSeats, onSeatsChange])

  const toggleSeat = (asiento) => {
    if (asiento.estado === 'ocupado') return

    const seatCode = `${asiento.sector}-${asiento.numero}`
    setSelectedSeats(prev => {
      if (prev.includes(seatCode)) {
        return prev.filter(s => s !== seatCode)
      } else {
        return [...prev, seatCode]
      }
    })
  }

  const getColorClass = (estado, isSelected) => {
    if (isSelected) return 'bg-red-600 border-red-700'
    switch (estado) {
      case 'libre':
        return 'bg-green-400 hover:bg-green-500 cursor-pointer'
      case 'ocupado':
        return 'bg-gray-400 cursor-not-allowed'
      case 'reservado':
        return 'bg-yellow-400 hover:bg-yellow-500 cursor-pointer'
      default:
        return 'bg-gray-300'
    }
  }

  return (
    <div className="space-y-8">
      {/* Leyenda */}
      <div className="flex flex-wrap gap-4 justify-center p-4 bg-gray-50 rounded-lg">
        <div className="flex items-center gap-2">
          <div className="w-6 h-6 bg-green-400 rounded"></div>
          <span className="text-gray-700">Disponible</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-6 h-6 bg-yellow-400 rounded"></div>
          <span className="text-gray-700">Reservado</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-6 h-6 bg-gray-400 rounded"></div>
          <span className="text-gray-700">Ocupado</span>
        </div>
        <div className="flex items-center gap-2">
          <div className="w-6 h-6 bg-red-600 rounded"></div>
          <span className="text-gray-700">Seleccionado</span>
        </div>
      </div>

      {/* Mapas de sectores */}
      {Object.entries(sectorMap).map(([sectorName, seatsList]) => (
        <div key={sectorName} className="border rounded-lg p-6 bg-gray-50">
          <h3 className="text-xl font-bold text-gray-900 mb-4">{sectorName}</h3>

          {/* Pantalla */}
          <div className="flex justify-center mb-6">
            <div className="w-full max-w-md h-12 bg-gradient-to-b from-gray-800 to-gray-600 rounded-full flex items-center justify-center">
              <span className="text-white font-bold">Escenario</span>
            </div>
          </div>

          {/* Grid de asientos */}
          <div className="flex justify-center">
            <div className="grid gap-2" style={{
              gridTemplateColumns: `repeat(${Math.ceil(Math.sqrt(seatsList.length))}, minmax(0, 1fr))`,
              maxWidth: '400px'
            }}>
              {seatsList.sort((a, b) => a.numero - b.numero).map(asiento => {
                const seatCode = `${asiento.sector}-${asiento.numero}`
                const isSelected = selectedSeats.includes(seatCode)

                return (
                  <button
                    key={asiento.id}
                    onClick={() => toggleSeat(asiento)}
                    disabled={asiento.estado === 'ocupado'}
                    className={`w-8 h-8 rounded-sm border transition-all ${getColorClass(asiento.estado, isSelected)}`}
                    title={`${asiento.sector} - Asiento ${asiento.numero}`}
                  >
                    <span className="text-xs font-semibold text-white opacity-0">
                      {asiento.numero}
                    </span>
                  </button>
                )
              })}
            </div>
          </div>

          {/* Numeración */}
          <div className="mt-4 text-xs text-gray-600 text-center">
            Asientos disponibles: {seatsList.filter(s => s.estado === 'libre').length}
          </div>
        </div>
      ))}
    </div>
  )
}
