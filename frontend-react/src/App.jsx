import Header from './components/Header'
import Navbar from './components/Navbar'
import Footer from './components/Footer'


export default function App() {
  return (
    <>
      <Header />
      <Navbar />
      <main className="container">
        <HomePage />
      </main>
      <Footer />
    </>
  )
}