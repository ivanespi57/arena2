import { useState, useEffect } from 'react'
import { useNavigate } from 'react-router-dom'
import { useCartStore } from '@/store/cartStore'
import { useAuthStore } from '@/store/authStore'
import apiClient from '@/api/client'
import { Clock, Trash2, Loader } from 'lucide-react'

export default function CartPage() {
  const navigate = useNavigate()
  const { items, expiryTime, clearCart } = useCartStore()
  const { token } = useAuthStore()
  const [timeLeft, setTimeLeft] = useState('')
  const [isProcessing, setIsProcessing] = useState(false)

  useEffect(() => {
    const updateTimer = () => {
      if (!expiryTime) return

      const now = new Date()
      const expiry = new Date(expiryTime)
      const diff = expiry - now

      if (diff <= 0) {
        clearCart()
        navigate('/')
        return
      }

      const minutes = Math.floor(diff / 60000)
      const seconds = Math.floor((diff % 60000) / 1000)
      setTimeLeft(`${minutes}:${seconds.toString().padStart(2, '0')}`)
    }

    updateTimer()
    const interval = setInterval(updateTimer, 1000)
    return () => clearInterval(interval)
  }, [expiryTime, clearCart, navigate])

  const total = items.reduce((sum, item) => sum + (item.precio * 1), 0)

  const handleConfirmPurchase = async () => {
    setIsProcessing(true)
    try {
      const response = await apiClient.post('/compra', {
        items: items.map(item => ({
          evento_id: item.eventoId,
          asiento_id: item.asientoId,
          precio_id: item.precioId,
        })),
      })

      clearCart()
      navigate(`/mis-entradas`)
    } catch (error) {
      alert('Error al procesar la compra: ' + error.response?.data?.message)
    } finally {
      setIsProcessing(false)
    }
  }

  if (items.length === 0) {
    return (
      <div className="bg-white rounded-lg shadow-lg p-12 text-center">
        <h2 className="text-2xl font-bold text-gray-900 mb-4">Tu carrito está vacío</h2>
        <p className="text-gray-600 mb-6">Explora nuestros eventos y agrega entradas a tu carrito</p>
        <button
          onClick={() => navigate('/')}
          className="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition"
        >
          Ver Eventos
        </button>
      </div>
    )
  }

  return (
    <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
      {/* Lista de items */}
      <div className="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
        <h2 className="text-2xl font-bold text-gray-900 mb-6">Tu Carrito</h2>

        {/* Timer */}
        {timeLeft && (
          <div className="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex items-center gap-2">
            <Clock className="w-5 h-5 text-yellow-600" />
            <span className="text-yellow-700">
              <strong>Expira en:</strong> {timeLeft}
            </span>
          </div>
        )}

        <div className="space-y-4">
          {items.map((item) => (
            <div key={item.id} className="flex justify-between items-center p-4 border rounded-lg hover:bg-gray-50">
              <div>
                <p className="font-medium text-gray-900">{item.eventoNombre}</p>
                <p className="text-sm text-gray-600">Asiento: {item.asientoNumero}</p>
              </div>
              <div className="flex items-center gap-4">
                <span className="text-red-600 font-bold">${item.precio.toFixed(2)}</span>
                <button
                  onClick={() => {
                    // Remover item del carrito
                    console.log('Remover:', item.id)
                  }}
                  className="text-red-600 hover:text-red-700 p-2"
                >
                  <Trash2 className="w-5 h-5" />
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>

      {/* Resumen de compra */}
      <div className="bg-white rounded-lg shadow-lg p-6 h-fit">
        <h3 className="text-xl font-bold text-gray-900 mb-4">Resumen de Compra</h3>

        <div className="space-y-3 mb-6">
          <div className="flex justify-between text-gray-700">
            <span>Entradas ({items.length})</span>
            <span>${total.toFixed(2)}</span>
          </div>
          <div className="flex justify-between text-gray-700">
            <span>Gastos de procesamiento</span>
            <span>$0.00</span>
          </div>

          <div className="border-t pt-3 flex justify-between">
            <span className="font-bold text-gray-900">Total</span>
            <span className="text-2xl font-bold text-red-600">${total.toFixed(2)}</span>
          </div>
        </div>

        <button
          onClick={handleConfirmPurchase}
          disabled={isProcessing}
          className="w-full bg-red-600 hover:bg-red-700 disabled:bg-red-400 text-white font-bold py-3 rounded-lg transition flex items-center justify-center gap-2"
        >
          {isProcessing && <Loader className="w-4 h-4 animate-spin" />}
          {isProcessing ? 'Procesando...' : 'Confirmar Compra'}
        </button>

        <button
          onClick={() => navigate('/')}
          className="w-full mt-3 border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold py-3 rounded-lg transition"
        >
          Continuar Comprando
        </button>
      </div>
    </div>
  )
}
