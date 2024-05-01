import React, { useState, useRef, useEffect } from "@wordpress/element";
import Select from "react-select";
import Alert from "./Alert";
import { useGlobalContext } from "../context";

const colourStyles = {
    control: (styles, { selectProps }) => ({
        ...styles,
        backgroundColor: "white",
        boxShadow: "none",
        fontSize: selectProps.fontSize,
        borderColor: "hsl(0, 0%, 80%)",
        color: "#000",
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
function Header() {
    const {
        setModalOpen,
        selectedRole,
        setSelectedRole,
        userRolesDropdown,
        setUserRolesDropdown,
        roleInfo,
        setRoleInfo,
        toBeDeletedCaps,
        setToBeDeletedCaps,
        roleCapabilities,
        alert,
        showAlert,
    } = useGlobalContext();

    const modalOpenHandler = (type) => {
        if (type === "rename_role") {
            setRoleInfo({
                ...roleInfo,
                role_id: selectedRole.value,
                role_name: selectedRole.label,
            });
        }
        if (type === "delete_capability") {
            let newRolesCaps = roleCapabilities
                .filter((item) => {
                    return item.cap_id.split("_")[0] === "adminify";
                    // return item.cap_id.indexOf('adminify_') > -1
                })
                .map((item, i) => {
                    item.delete = false;
                    return item;
                });
            setToBeDeletedCaps(newRolesCaps);
        }
        setModalOpen({ status: true, type: type });
    };

    const roleHandler = (value) => {
        setSelectedRole(value);
    };

    return (
        <>
            {alert.show && <Alert {...alert} removeAlert={showAlert} />}
            <div className="adminify-user-role-editor-header">
                <div className="adminify-user-role-editor-heading wp-heading-inline is-pulled-left is-flex is-align-items-center mt-3">
                    Role Capabilities &nbsp;
                    <Select
                        fontSize="16px"
                        className="user-role-select-container"
                        classNamePrefix="user-role-select"
                        options={userRolesDropdown}
                        value={selectedRole}
                        onChange={roleHandler}
                        placeholder="Select Role"
                        styles={colourStyles}
                    />
                </div>

                <div className="wp-adminify--page--title--actions is-pulled-right">
                    <button
                        onClick={() => modalOpenHandler("delete_capability")}
                        className="page-title-action mr-3 user-role-editor-action-button user-role-editor-button-danger">
                        Delete Capability
                    </button>
                    <button
                        onClick={() => modalOpenHandler("add_capability")}
                        className="page-title-action mr-3 user-role-editor-action-button">
                        Add Capability
                    </button>
                    <button
                        onClick={() => modalOpenHandler("delete_role")}
                        className="page-title-action mr-3 user-role-editor-action-button user-role-editor-button-danger">
                        Delete Role
                    </button>
                    <button
                        onClick={() => modalOpenHandler("rename_role")}
                        className="page-title-action mr-3 user-role-editor-action-button">
                        Rename Role
                    </button>
                    <button
                        onClick={() => modalOpenHandler("add_role")}
                        className="page-title-action mr-3 user-role-editor-action-button">
                        Add Role
                    </button>
                </div>
            </div>
        </>
    );
}

export default Header;
