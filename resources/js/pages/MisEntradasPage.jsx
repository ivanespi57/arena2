import { useQuery } from '@tanstack/react-query'
import { useRef, useEffect } from 'react'
import QRCodeStyling from 'qr-code-styling'
import apiClient from '@/api/client'
import { Loader, Download, Printer } from 'lucide-react'

function fetchMisEntradas() {
  return apiClient.get('/mis-entradas').then(res => res.data)
}

export default function MisEntradasPage() {
  const { data: entradas = [], isLoading, error } = useQuery({
    queryKey: ['misEntradas'],
    queryFn: fetchMisEntradas,
  })

  const handleDownloadQR = (entrada) => {
    const qr = document.querySelector(`#qr-${entrada.id}`)
    if (qr) {
      const canvas = qr.querySelector('canvas')
      const url = canvas.toDataURL('image/png')
      const link = document.createElement('a')
      link.href = url
      link.download = `entrada-${entrada.numero}.png`
      link.click()
    }
  }

  const handlePrint = (entrada) => {
    window.print()
  }

  if (isLoading) {
    return (
      <div className="flex justify-center items-center min-h-[60vh]">
        <Loader className="w-8 h-8 animate-spin text-red-600" />
      </div>
    )
  }

  if (error || !entradas.length) {
    return (
      <div className="bg-white rounded-lg shadow-lg p-12 text-center">
        <p className="text-gray-600 text-lg">No tienes entradas aún</p>
      </div>
    )
  }

  return (
    <div className="space-y-8">
      <div className="text-center">
        <h1 className="text-4xl font-bold text-gray-900">Mis Entradas</h1>
        <p className="text-gray-600 mt-2">Aquí están tus entradas con QR para acceder a los eventos</p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {entradas.map((entrada) => (
          <div key={entrada.id} className="bg-white rounded-lg shadow-lg overflow-hidden">
            {/* Encabezado con evento */}
            <div className="bg-gradient-to-r from-red-600 to-red-700 p-6 text-white">
              <h3 className="text-2xl font-bold">{entrada.evento.nombre}</h3>
              <p className="text-red-100 mt-1">
                {new Date(entrada.evento.fecha).toLocaleDateString('es-ES')}
              </p>
            </div>

            {/* Contenido */}
            <div className="p-6 space-y-4">
              <div className="grid grid-cols-2 gap-4 text-sm">
                <div>
                  <p className="text-gray-600">Asiento</p>
                  <p className="font-bold text-gray-900">{entrada.asiento.numero}</p>
                </div>
                <div>
                  <p className="text-gray-600">Sector</p>
                  <p className="font-bold text-gray-900">{entrada.asiento.sector}</p>
                </div>
                <div>
                  <p className="text-gray-600">Categoría</p>
                  <p className="font-bold text-gray-900">{entrada.precio.nombre}</p>
                </div>
                <div>
                  <p className="text-gray-600">Precio</p>
                  <p className="font-bold text-red-600">${entrada.precio.precio.toFixed(2)}</p>
                </div>
              </div>

              {/* QR */}
              <div className="flex justify-center p-4 bg-gray-50 rounded-lg">
                <div id={`qr-${entrada.id}`}>
                  <QRCode
                    value={`entrada_${entrada.id}`}
                    size={200}
                    level="H"
                    includeMargin={true}
                  />
                </div>
              </div>

              {/* Número de entrada */}
              <p className="text-center text-sm text-gray-600">
                Entrada #: <span className="font-mono font-bold">{entrada.numero}</span>
              </p>

              {/* Botones de acción */}
              <div className="flex gap-2">
                <button
                  onClick={() => handleDownloadQR(entrada)}
                  className="flex-1 flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition"
                >
                  <Download className="w-4 h-4" />
                  Descargar QR
                </button>
                <button
                  onClick={() => handlePrint(entrada)}
                  className="flex-1 flex items-center justify-center gap-2 bg-gray-600 hover:bg-gray-700 text-white py-2 rounded-lg transition"
                >
                  <Printer className="w-4 h-4" />
                  Imprimir
                </button>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}
