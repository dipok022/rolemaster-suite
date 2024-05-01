
import {useGlobalContext} from '../context'

const SaveButton = () => {
    const {saveUserRoleCapabilities} = useGlobalContext();
  return (
    <button className="page-title-action mr-3" onClick={saveUserRoleCapabilities}>
        Save Changes
    </button>
  )
}

export default SaveButton
