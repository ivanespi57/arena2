import { useState } from 'react'
import { useQuery } from '@tanstack/react-query'
import apiClient from '@/api/client'
import AdminEventForm from '@/components/AdminEventForm'
import AdminSectorForm from '@/components/AdminSectorForm'
import AdminEventList from '@/components/AdminEventList'
import AdminSectorList from '@/components/AdminSectorList'
import { Loader, Plus } from 'lucide-react'

function fetchEventosAdmin() {
  return apiClient.get('/admin/eventos').then(res => res.data)
}

function fetchSectoresAdmin() {
  return apiClient.get('/admin/sectores').then(res => res.data)
}

export default function AdminPage() {
  const [activeTab, setActiveTab] = useState('eventos')
  const [showForm, setShowForm] = useState(false)
  const [editingId, setEditingId] = useState(null)

  const { data: eventos = [], isLoading: eventosLoading, refetch: refetchEventos } = useQuery({
    queryKey: ['admin-eventos'],
    queryFn: fetchEventosAdmin,
  })

  const { data: sectores = [], isLoading: sectoresLoading, refetch: refetchSectores } = useQuery({
    queryKey: ['admin-sectores'],
    queryFn: fetchSectoresAdmin,
  })

  const handleCloseForm = () => {
    setShowForm(false)
    setEditingId(null)
  }

  const handleFormSuccess = () => {
    if (activeTab === 'eventos') {
      refetchEventos()
    } else {
      refetchSectores()
    }
    handleCloseForm()
  }

  return (
    <div className="space-y-8">
      <div className="text-center mb-8">
        <h1 className="text-4xl font-bold text-gray-900">Panel de Administración</h1>
        <p className="text-gray-600 mt-2">Gestiona eventos y sectores</p>
      </div>

      {/* Tabs */}
      <div className="flex gap-4 border-b border-gray-200">
        <button
          onClick={() => {
            setActiveTab('eventos')
            handleCloseForm()
          }}
          className={`px-6 py-3 font-medium transition ${
            activeTab === 'eventos'
              ? 'border-b-2 border-red-600 text-red-600'
              : 'text-gray-600 hover:text-gray-900'
          }`}
        >
          Eventos
        </button>
        <button
          onClick={() => {
            setActiveTab('sectores')
            handleCloseForm()
          }}
          className={`px-6 py-3 font-medium transition ${
            activeTab === 'sectores'
              ? 'border-b-2 border-red-600 text-red-600'
              : 'text-gray-600 hover:text-gray-900'
          }`}
        >
          Sectores
        </button>
      </div>

      {/* Contenido */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Formulario */}
        <div className="lg:col-span-1">
          {!showForm ? (
            <button
              onClick={() => setShowForm(true)}
              className="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center gap-2 transition"
            >
              <Plus className="w-5 h-5" />
              Agregar {activeTab === 'eventos' ? 'Evento' : 'Sector'}
            </button>
          ) : (
            <div className="bg-white rounded-lg shadow-lg p-6">
              {activeTab === 'eventos' ? (
                <AdminEventForm
                  onSuccess={handleFormSuccess}
                  onCancel={handleCloseForm}
                  editingId={editingId}
                />
              ) : (
                <AdminSectorForm
                  onSuccess={handleFormSuccess}
                  onCancel={handleCloseForm}
                  editingId={editingId}
                />
              )}
            </div>
          )}
        </div>

        {/* Lista */}
        <div className="lg:col-span-2">
          {activeTab === 'eventos' ? (
            <>
              {eventosLoading ? (
                <div className="flex justify-center py-12">
                  <Loader className="w-8 h-8 animate-spin text-red-600" />
                </div>
              ) : (
                <AdminEventList
                  eventos={eventos}
                  onEdit={(id) => {
                    setEditingId(id)
                    setShowForm(true)
                  }}
                  onDeleteSuccess={() => refetchEventos()}
                />
              )}
            </>
          ) : (
            <>
              {sectoresLoading ? (
                <div className="flex justify-center py-12">
                  <Loader className="w-8 h-8 animate-spin text-red-600" />
                </div>
              ) : (
                <AdminSectorList
                  sectores={sectores}
                  onEdit={(id) => {
                    setEditingId(id)
                    setShowForm(true)
                  }}
                  onDeleteSuccess={() => refetchSectores()}
                />
              )}
            </>
          )}
        </div>
      </div>
    </div>
  )
}
