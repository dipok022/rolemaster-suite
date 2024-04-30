import React, { useRef, useState, useEffect } from "react";
import Select from "react-select";
import Api from "../services/Api";
import { useGlobalContext } from "../context";

const colourStyles = {
    control: (styles, { selectProps }) => ({
        ...styles,
        backgroundColor: "white",
        boxShadow: "none",
        fontSize: selectProps.fontSize,
        borderColor: "hsl(0, 0%, 80%)",
        color: "#000",
        width: "250px",
    }),
    option: (styles, { selectProps, data, isDisabled, isFocused, isSelected }) => {
        return {
            ...styles,
            color: "#000",
            fontSize: selectProps.fontSize,
            padding: "2px 10px",
            backgroundColor: "transparent",
            color: isFocused || isSelected ? "#0347ff" : "",
        };
    },
};

const Modal = () => {
    const [makeCopyOfCaps, setMakeCopyOfCaps] = useState(null);
    const [deleteRole, setDeleteRole] = useState(null);
    const [capId, setCapId] = useState("adminify_");

    const {
        modalOpen,
        setModalOpen,
        userRolesDropdown,
        selectedRole,
        userRoleCapabilities,
        setUserRoleCapabilities,
        fetchUserRoles,
        roleInfo,
        setRoleInfo,
        roleCapabilities,
        toBeDeletedCaps,
        setToBeDeletedCaps,
        showAlert,
    } = useGlobalContext();
    const imagePath = window.WPAdminifyUserRoleEditor.image_path;

    let noneRolesDropdown = [{ value: "none", label: "None" }];
    let formatedUserRolesDropdown = noneRolesDropdown.concat(userRolesDropdown);
    let toBeDeletedUserRolesDropdown = userRolesDropdown.filter((item) => {
        return item.value.indexOf("adminify_") > -1;
    });
    toBeDeletedUserRolesDropdown = noneRolesDropdown.concat(toBeDeletedUserRolesDropdown);

    const roleHandler = (e, type = "") => {
        if (type === "add_role") {
            let name = e.target.name;
            let value = e.target.value;
            setRoleInfo({
                ...roleInfo,
                [name]: value,
                role_id: value.replace(/ /g, "_").toLowerCase(),
            });
        } else {
            let name = e.target.name;
            let value = e.target.value;
            setRoleInfo({ ...roleInfo, [name]: value });
        }
    };

    const roleCopyHandler = (role) => {
        if (role.value !== "none") {
            let selectedCaps = userRoleCapabilities[role.value].capabilities;
            setMakeCopyOfCaps(selectedCaps);
        } else {
            setMakeCopyOfCaps(null);
        }
    };

    const roleDeleteHandler = (role) => {
        if (role.value.indexOf("adminify_") > -1) {
            setDeleteRole(role);
        }
    };

    const modalCloseHandler = () => {
        setMakeCopyOfCaps(null);
        setRoleInfo({ role_id: "", role_name: "" });
        setToBeDeletedCaps([]);
        setDeleteRole(null);
        setModalOpen({ ...modalOpen, status: false });
    };

    const addNewRole = async (value) => {
        try {
            const { statusText, data } = await Api.post("/save-user-role", value);
            let { status, message, saved_role } = JSON.parse(data);
            if (statusText === "OK" && status === true) {
                // showAlert(true,message)
                let { adminify_role_id, name, capabilities } = saved_role;
                const newUserRoleCaps = {
                    ...userRoleCapabilities,
                    [adminify_role_id]: { name, capabilities },
                };
                setUserRoleCapabilities(newUserRoleCaps);
                fetchUserRoles();
                modalCloseHandler();
                showAlert(true, message);
            }
        } catch (error) {
            console.log(error);
        }
    };

    const deleteRoleFromDb = async (role) => {
        try {
            const { statusText, data } = await Api.post("/delete-user-role", role);
            let { status, message, deleted_role } = JSON.parse(data);
            if (statusText === "OK" && status === true) {
                fetchUserRoles();
                modalCloseHandler();
                showAlert(true, message);
            }
        } catch (error) {
            console.log(error);
        }
    };

    const renameRole = async (roleInfo) => {
        try {
            const { statusText, data } = await Api.post("/rename-user-role", roleInfo);
            let { status, message, renamed_to } = JSON.parse(data);
            if (statusText === "OK" && status === true && renamed_to === roleInfo.role_name) {
                // make active role that is currenly open
                const activeRole = { value: roleInfo.role_id, label: roleInfo.role_name };
                fetchUserRoles(activeRole);
                modalCloseHandler();
                showAlert(true, message);
            }
        } catch (error) {
            console.log(error);
        }
    };

    const handleCapability = (e) => {
        const prefix = "adminify_";
        const input = e.target.value;
        let value = prefix + input.substr(prefix.length);
        setCapId(value);
    };

    const addCapability = async (role, capId) => {
        try {
            const { statusText, data } = await Api.post("/save-new-capability", { role, capId });
            let { status, message, saved_cap } = JSON.parse(data);
            if (statusText === "OK" && status === true && saved_cap === capId) {
                fetchUserRoles(selectedRole);
                modalCloseHandler();
                showAlert(true, message);
            }
        } catch (error) {
            console.log(error);
        }
    };

    const deleteCapability = async () => {
        const newToBeDeleted = toBeDeletedCaps
            .filter((item) => {
                return item.delete;
            })
            .map((item) => {
                return item.cap_id;
            });
        try {
            const { statusText, data } = await Api.post("/delete-capabilities", {
                delete_caps: newToBeDeleted,
            });
            let { status, message } = JSON.parse(data);
            if (statusText === "OK" && status === true) {
                fetchUserRoles(selectedRole);
                modalCloseHandler();
                showAlert(true, message);
            }
        } catch (error) {
            console.log(error);
        }
    };

    const handleSubmit = () => {
        if (modalOpen.type === "add_role" || modalOpen.type === "rename_role") {
            if (!roleInfo.role_id.trim()) alert('Please fill out the "Role Name (ID)"');
            if (!roleInfo.role_name.trim()) alert('Please fill out the "Display Role Name"');

            if (modalOpen.type === "add_role" && roleInfo.role_id && roleInfo.role_name) {
                let capabilities = {};
                if (makeCopyOfCaps !== null) {
                    capabilities = makeCopyOfCaps;
                }
                addNewRole({ ...roleInfo, capabilities });
            } else if (modalOpen.type === "rename_role") {
                renameRole(roleInfo);
            }
        } else if (modalOpen.type === "delete_role") {
            deleteRoleFromDb(deleteRole);
        } else if (modalOpen.type === "add_capability") {
            addCapability(selectedRole.value, capId);
        } else if (modalOpen.type === "delete_capability") {
            deleteCapability();
        }
    };

    const handleSelectAllCaps = (e) => {
        const newToBeDeleted = toBeDeletedCaps.map((item) => {
            if (e.target.checked) {
                item.delete = true;
            } else {
                item.delete = false;
            }
            return item;
        });
        setToBeDeletedCaps(newToBeDeleted);
    };

    const handleCapabilityToDelete = (cap_id, checked) => {
        const newToBeDeleted = toBeDeletedCaps.map((item) => {
            if (item.cap_id == cap_id) {
                item.delete = checked;
            }
            return item;
        });
        setToBeDeletedCaps(newToBeDeleted);
    };

    useEffect(() => {
        setDeleteRole(toBeDeletedUserRolesDropdown[0]);
    }, []);

    return (
        <div className={`user-role-editor-modal-wrap`}>
            <div className={`aim-modal ${modalOpen.status ? "aim-open" : "aim-close"}`}>
                <div className="aim-modal--content" style={{ overflow: "visible" }}>
                    <div className="aim-modal--header">
                        <div className="aim-modal--header-logo-area">
                            <span className="aim-modal--header-logo-title">
                                <img src={`${imagePath}logos/menu-icon.svg`} />
                                WP Adminify - {modalOpen.type.replace("_", " ")}
                            </span>
                        </div>
                        <div className="aim-modal--header-close-btn" onClick={modalCloseHandler}>
                            <i className="dashicons dashicons-no-alt" title="Close"></i>
                        </div>
                    </div>
                    <div className="aim-modal--body" style={{ overflowY: "unset" }}>
                        {modalOpen.type === "delete_capability" && (
                            <div>
                                <div className="delete-capabilities">
                                    <ul>
                                        <li className="select-all">
                                            <label htmlFor="select_all">
                                                <input
                                                    id="select_all"
                                                    type="checkbox"
                                                    onChange={handleSelectAllCaps}
                                                />{" "}
                                                Select All
                                            </label>
                                        </li>
                                        {toBeDeletedCaps &&
                                            toBeDeletedCaps.map((item, i) => {
                                                return (
                                                    <li key={i}>
                                                        <label htmlFor={`de_${item.cap_id}`}>
                                                            <input
                                                                type="checkbox"
                                                                id={`de_${item.cap_id}`}
                                                                checked={item.delete}
                                                                onChange={(e) =>
                                                                    handleCapabilityToDelete(
                                                                        item.cap_id,
                                                                        e.target.checked
                                                                    )
                                                                }
                                                            />{" "}
                                                            {item.cap_id}
                                                        </label>
                                                    </li>
                                                );
                                            })}
                                    </ul>
                                </div>
                            </div>
                        )}
                        {modalOpen.type === "add_capability" && (
                            <div>
                                <label htmlFor="">Capability name (ID):</label>
                                <input
                                    name="capability_id"
                                    type="text"
                                    value={capId}
                                    onChange={handleCapability}
                                />
                            </div>
                        )}
                        {(modalOpen.type === "add_role" ||
                            modalOpen.type === "rename_role" ||
                            modalOpen.type === "delete_role") && (
                            <>
                                {(modalOpen.type === "add_role" ||
                                    modalOpen.type === "rename_role") && (
                                    <>
                                        <div>
                                            <label htmlFor="">Display Role Name:</label>
                                            <input
                                                name="role_name"
                                                type="text"
                                                value={roleInfo.role_name}
                                                onChange={(e) =>
                                                    roleHandler(
                                                        e,
                                                        modalOpen.type === "add_role"
                                                            ? "add_role"
                                                            : ""
                                                    )
                                                }
                                            />
                                        </div>
                                        <div>
                                            <label htmlFor="">Role name (ID):</label>
                                            <input
                                                name="role_id"
                                                type="text"
                                                placeholder={roleInfo.role_id}
                                                value={
                                                    modalOpen.type !== "rename_role"
                                                        ? roleInfo.role_id
                                                        : ""
                                                }
                                                disabled={
                                                    modalOpen.type === "rename_role" ? true : false
                                                }
                                                onChange={roleHandler}
                                            />
                                        </div>
                                    </>
                                )}
                                {modalOpen.type !== "rename_role" &&
                                    modalOpen.type !== "delete_role" && (
                                        <div>
                                            <label htmlFor="">Make copy of:</label>
                                            <div style={{ width: "400px", display: "inline-flex" }}>
                                                <Select
                                                    fontSize="16px"
                                                    className="capabilities-container"
                                                    classNamePrefix="capabilities"
                                                    options={formatedUserRolesDropdown}
                                                    defaultValue={formatedUserRolesDropdown[0]}
                                                    onChange={roleCopyHandler}
                                                    placeholder="Select Role"
                                                    styles={colourStyles}
                                                />
                                            </div>
                                        </div>
                                    )}
                                {modalOpen.type === "delete_role" && (
                                    <div>
                                        <label htmlFor="">Select Role:</label>
                                        <div style={{ width: "400px", display: "inline-flex" }}>
                                            <Select
                                                fontSize="16px"
                                                className="capabilities-container"
                                                classNamePrefix="capabilities"
                                                options={toBeDeletedUserRolesDropdown}
                                                value={toBeDeletedUserRolesDropdown[0]}
                                                onChange={roleDeleteHandler}
                                                placeholder="Select Role"
                                                styles={colourStyles}
                                                isSearchable={false}
                                            />
                                        </div>
                                    </div>
                                )}
                            </>
                        )}
                    </div>
                    <div className="aim-modal--footer">
                        <button className={modalOpen.type.replace("_", "-")} onClick={handleSubmit}>
                            {modalOpen.type.replace("_", " ")}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default Modal;
