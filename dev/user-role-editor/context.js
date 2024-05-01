import { createContext, useState, useEffect, useContext } from "@wordpress/element";
import Api from "./services/Api";

const AppContext = createContext();

const AppProvider = ({ children }) => {
    const [alert, setAlert] = useState({ show: false, message: "", reload: false });
    const [isLoading, setIsLoading] = useState(true);
    const [isError, setError] = useState(false);
    const [modalOpen, setModalOpen] = useState({ status: false, type: "add_role" });
    const [column, setColumn] = useState(3);
    const [selectedRole, setSelectedRole] = useState(null);
    const [userRoleCapabilities, setUserRoleCapabilities] = useState(null);
    const [postTypes, setPostTypes] = useState([]);

    const [roleInfo, setRoleInfo] = useState({ role_id: "", role_name: "" });

    const [activeGroup, setActiveGroup] = useState("editing");
    const [userRolesDropdown, setUserRolesDropdown] = useState(null);
    const [roleCapabilities, setRoleCapabilities] = useState(null);

    const [selectAll, setSelectAll] = useState(false);
    const [denyAll, setDenyAll] = useState(false);
    const [toBeDeletedCaps, setToBeDeletedCaps] = useState([]);

    const showAlert = (show = false, message = "", reload = false) => {
        setAlert({ show, message, reload });
    };

    const handleActiveGroup = (e) => {
        let group = e.target.textContent.toLowerCase();
        setSelectAll(false);
        setDenyAll(false);
        setActiveGroup(group);
    };

    const fetchUserRoles = async (role = null) => {
        try {
            const response = await Api.get("/get-user-role-capabilities", {});
            const post_types = Object.keys(response.data.get_post_types);
            setPostTypes(post_types);

            const userRoles = processedUserRolesDropDown(response.data.user_role_capabilities);
            setUserRolesDropdown(userRoles);

            const activeRole = role
                ? role
                : userRoles.filter((option) => option.value == "subscriber")[0];
            setSelectedRole(activeRole);

            let allCapabilities = processedDefaultCapabilities(
                response.data.user_role_capabilities,
                activeRole.value,
                post_types
            );
            setRoleCapabilities(allCapabilities);

            setUserRoleCapabilities(response.data.user_role_capabilities);
        } catch (error) {
            setError(true);
            console.log(error);
        }
        setIsLoading(false);
    };

    const processedUserRolesDropDown = (role_list) => {
        let roles = [];
        for (let key in role_list) {
            let caps = role_list[key].capabilities;
            roles.push({ value: key, label: role_list[key].name });
        }
        return roles;
    };

    const processedDefaultCapabilities = (role_list, active_role, post_types) => {
        let allCapabilities = role_list.administrator.capabilities;

        let activeCapabilities = Object.keys(role_list[active_role].capabilities);

        allCapabilities = Object.keys(allCapabilities).map((cap) => {
            let status = activeCapabilities.indexOf(cap) !== -1 ? true : false;

            let group = "additional";
            // if ((/edit/.test(cap) || /create/.test(cap)) && (/posts/.test(cap)|| /pages/.test(cap)) || ('publish_posts' === cap) || ('publish_pages' === cap)) group = 'editing'
            post_types.forEach((post) => {
                if (
                    (cap.indexOf(post) > -1 && cap.indexOf("edit") > -1) ||
                    (cap.indexOf(post) > -1 && cap.indexOf(`publish_${post}`) > -1) ||
                    (cap.indexOf(post) > -1 && cap.indexOf("create") > -1)
                ) {
                    group = "editing";
                }
            });
            post_types.forEach((post) => {
                if (cap.indexOf(post) > -1 && cap.indexOf("delete") > -1) {
                    group = "deletion";
                }
            });
            post_types.forEach((post) => {
                if (
                    cap.indexOf(post) > -1 &&
                    cap.indexOf("read") > -1 &&
                    cap.indexOf("private") > -1
                ) {
                    group = "reading";
                }
            });

            // if ((/delete/.test(cap)) && ((/posts/.test(cap)) || (/pages/.test(cap)))) group = 'deletion'

            // if ((/read/.test(cap)) && (/private/.test(cap)) && ((/posts/.test(cap)) || (/pages/.test(cap)))) group = 'reading'
            if (/manage/.test(cap) && (/tags/.test(cap) || /categories/.test(cap)))
                group = "taxonomies";
            if (/files/.test(cap) || /upload/.test(cap)) group = "media";
            if (/comments/.test(cap)) group = "comments";
            if (/users/.test(cap)) group = "users";
            if (
                "manage_options" === cap ||
                "edit_dashboard" === cap ||
                "export" === cap ||
                "import" === cap ||
                "update_core" === cap ||
                "unfiltered_html" === cap ||
                "read" === cap
            )
                group = "admin";
            if (/theme/.test(cap)) group = "themes";
            if (/plugins/.test(cap)) group = "plugins";

            return { cap_id: cap, status, group };
        });

        return allCapabilities;
    };

    const saveUserRoleCapabilities = () => {
        let role = selectedRole.value;
        let caps = roleCapabilities
            .filter((item) => {
                return item.status;
            })
            .map((item) => {
                return item.cap_id;
            });
        updateCapabilitiesToUSer({ role, caps });
    };

    const updateCapabilitiesToUSer = async (value) => {
        try {
            const { statusText, data } = await Api.post("/update-capabilities", value);
            let { status, message } = JSON.parse(data);
            if (statusText === "OK" && status === true) {
                fetchUserRoles(selectedRole);
                showAlert(true, message);
            }
        } catch (error) {
            console.log(error);
        }
    };

    useEffect(() => {
        fetchUserRoles();
    }, []);

    return (
        <AppContext.Provider
            value={{
                isLoading,
                setIsLoading,
                isError,
                setError,
                userRoleCapabilities,
                setUserRoleCapabilities,
                modalOpen,
                setModalOpen,
                selectedRole,
                setSelectedRole,
                column,
                setColumn,
                userRolesDropdown,
                setUserRolesDropdown,
                roleCapabilities,
                processedDefaultCapabilities,
                setRoleCapabilities,
                postTypes,
                activeGroup,
                handleActiveGroup,
                fetchUserRoles,
                roleInfo,
                setRoleInfo,
                toBeDeletedCaps,
                setToBeDeletedCaps,
                saveUserRoleCapabilities,
                alert,
                showAlert,
                selectAll,
                setSelectAll,
                denyAll,
                setDenyAll,
            }}>
            {children}
        </AppContext.Provider>
    );
};

export const useGlobalContext = () => {
    return useContext(AppContext);
};

export { AppContext, AppProvider };
