import React from 'react'
import './navbar.css'

import house from '../../svg/house-svgrepo-com.svg'

export default function Navbar() {

    return (
        <header
            className="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
            <div className="col-md-3 mb-2 mb-md-0">
                <a href="/" className="d-inline-flex link-body-emphasis text-decoration-none">
                    <img className="livin-svg-placeholder" src={house} alt="image-house"/>
                </a>
            </div>

            <ul className="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
                <li><a href="#" className="nav-link px-2 link-secondary navbar-fonter">Home</a></li>
                <li><a href="#quienes-somos" className="nav-link px-2 navbar-fonter">Quienes Somos</a></li>
                <li><a href="#" className="nav-link px-2 navbar-fonter">Rentar</a></li>
                <li><a href="#" className="nav-link px- navbar-fonter2">Comprar</a></li>
                <li><a href="#" className="nav-link px- navbar-fonter2">About</a></li>
            </ul>
            <div className="col-md-3 text-end">
                <button type="button" className="btn btn-light">Iniciar sesion</button>
                <button type="button" className="btn btn-dark">Registrarse</button>
            </div>
        </header>
    )
}

/**/