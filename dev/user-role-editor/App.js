import React, { useState } from "@wordpress/element";
import Loader from "./components/Loader";
import Header from "./components/Header";
import Body from "./components/Body";
// import Modal from "./components/Modal";
import { useGlobalContext } from "./context";

function App() {
    const { isLoading, isError } = useGlobalContext();
    if (isLoading) {
        return <Loader />;
    }

    if (isError) {
        return "Error happend";
    }

    return (
        <>
            <div className="rolemaster-suite--user--role--editor--container">
                <Header />
                <Body />
            </div>
            {/* <Modal/> */}
        </>
    );
}

export default App;
