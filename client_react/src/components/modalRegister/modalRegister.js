
 import './modalRegister.css'


export default function ModalRegister(props) {
    return (
        <>
            <div className="overlay"></div>
            <div className="modal-content">
                <header className="modal__header">
                    <h2>Regístrate con nosotros</h2>
                    <button onClick={props.state} className="close-button">&times;</button>
                </header>
                <main className="modal__main">
                    <p>Some content here!</p>
                </main>
            </div>
        </>
    );
}
