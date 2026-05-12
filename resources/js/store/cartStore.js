import { create } from 'zustand'
import { devtools, persist } from 'zustand/middleware'

export const useCartStore = create(
  persist(
    devtools((set) => ({
      items: [],
      expiryTime: null,

      addItem: (item) => {
        set((state) => {
          // Expiración de 15 minutos
          const expiryTime = new Date(Date.now() + 15 * 60 * 1000)

          return {
            items: [...state.items, { ...item, id: Math.random() }],
            expiryTime,
          }
        })
      },

      removeItem: (itemId) => {
        set((state) => ({
          items: state.items.filter(item => item.id !== itemId),
        }))
      },

      clearCart: () => {
        set({ items: [], expiryTime: null })
      },

      isExpired: () => {
        const { expiryTime } = useCartStore.getState()
        return expiryTime && new Date() > new Date(expiryTime)
      },
    })),
    {
      name: 'cart-storage',
    }
  )
)
