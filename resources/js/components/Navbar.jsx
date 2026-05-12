import { Link, useNavigate } from 'react-router-dom'
import { useAuthStore } from '@/store/authStore'
import { Menu, X, LogOut, LayoutDashboard, Ticket } from 'lucide-react'
import { useState } from 'react'

export default function Navbar() {
  const { isAuthenticated, user, logout } = useAuthStore()
  const navigate = useNavigate()
  const [menuOpen, setMenuOpen] = useState(false)

  const handleLogout = () => {
    logout()
    navigate('/login')
    setMenuOpen(false)
  }

  return (
    <nav className="bg-white shadow-md sticky top-0 z-50">
      <div className="container mx-auto px-4 py-4">
        <div className="flex justify-between items-center">
          {/* Logo */}
          <Link to="/" className="flex items-center gap-2">
            <div className="text-2xl font-bold">
              <span className="text-gray-800">Roig</span>
              <span className="text-red-600"> Arena</span>
            </div>
          </Link>

          {/* Desktop Menu */}
          <div className="hidden md:flex items-center gap-6">
            <Link to="/" className="text-gray-700 hover:text-red-600 transition">
              Eventos
            </Link>

            {isAuthenticated && (
              <>
                <Link to="/mis-entradas" className="text-gray-700 hover:text-red-600 transition flex items-center gap-2">
                  <Ticket className="w-4 h-4" />
                  Mis Entradas
                </Link>

                {user?.role === 'admin' && (
                  <Link to="/admin" className="text-gray-700 hover:text-red-600 transition flex items-center gap-2">
                    <LayoutDashboard className="w-4 h-4" />
                    Admin
                  </Link>
                )}

                <div className="flex items-center gap-3">
                  <span className="text-gray-700">{user?.name}</span>
                  <button
                    onClick={handleLogout}
                    className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition"
                  >
                    <LogOut className="w-4 h-4" />
                    Salir
                  </button>
                </div>
              </>
            )}

            {!isAuthenticated && (
              <div className="flex items-center gap-3">
                <Link to="/login" className="text-gray-700 hover:text-red-600 transition">
                  Iniciar Sesión
                </Link>
                <Link
                  to="/register"
                  className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition"
                >
                  Registrarse
                </Link>
              </div>
            )}
          </div>

          {/* Mobile Menu Button */}
          <button
            onClick={() => setMenuOpen(!menuOpen)}
            className="md:hidden"
          >
            {menuOpen ? (
              <X className="w-6 h-6" />
            ) : (
              <Menu className="w-6 h-6" />
            )}
          </button>
        </div>

        {/* Mobile Menu */}
        {menuOpen && (
          <div className="md:hidden mt-4 space-y-3 pb-4">
            <Link
              to="/"
              onClick={() => setMenuOpen(false)}
              className="block text-gray-700 hover:text-red-600 transition"
            >
              Eventos
            </Link>

            {isAuthenticated && (
              <>
                <Link
                  to="/mis-entradas"
                  onClick={() => setMenuOpen(false)}
                  className="block text-gray-700 hover:text-red-600 transition"
                >
                  Mis Entradas
                </Link>

                {user?.role === 'admin' && (
                  <Link
                    to="/admin"
                    onClick={() => setMenuOpen(false)}
                    className="block text-gray-700 hover:text-red-600 transition"
                  >
                    Admin
                  </Link>
                )}

                <button
                  onClick={handleLogout}
                  className="w-full text-left bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition"
                >
                  Salir
                </button>
              </>
            )}

            {!isAuthenticated && (
              <>
                <Link
                  to="/login"
                  onClick={() => setMenuOpen(false)}
                  className="block text-gray-700 hover:text-red-600 transition"
                >
                  Iniciar Sesión
                </Link>
                <Link
                  to="/register"
                  onClick={() => setMenuOpen(false)}
                  className="block w-full text-center bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition"
                >
                  Registrarse
                </Link>
              </>
            )}
          </div>
        )}
      </div>
    </nav>
  )
}
