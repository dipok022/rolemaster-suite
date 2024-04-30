import React, {useEffect} from 'react'

const Alert = ({show,message,removeAlert,reload}) => {

    useEffect(()=>{
        const timeout = setTimeout(()=>{
                removeAlert()
                if(reload){
                    location.reload()
                }
            },1000);
        return () => clearTimeout(timeout);
    },[]);

    return (
        <div id="adminify-data-saved-message" className="is-primary">{message}</div>
    )
}

export default Alert