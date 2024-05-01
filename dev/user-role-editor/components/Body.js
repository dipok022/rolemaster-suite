import React, { useState, useEffect, useRef } from "@wordpress/element";
import Select from "react-select";
import SaveButton from "./SaveButton";
import { useGlobalContext } from "../context";

const options = [
    { value: 1, label: "Column 1" },
    { value: 2, label: "Column 2" },
    { value: 3, label: "Column 3" },
];
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
            backgroundColor: isFocused || isSelected ? "#f9f9f9" : "",
        };
    },
};

const isFloat = (value) => {
    if (typeof value === "number" && !Number.isNaN(value) && !Number.isInteger(value)) {
        return true;
    }
    return false;
};

const roleGroups = {
    editing: { label: "Editing" },
    deletion: { label: "Deletion" },
    reading: { label: "Reading" },
    taxonomies: { label: "Taxonomies" },
    media: { label: "Media" },
    comments: { label: "Comments" },
    users: { label: "Users" },
    admin: { label: "Admin" },
    themes: { label: "Themes" },
    plugins: { label: "Plugins" },
    additional: { label: "Additional" },
};

const Body = () => {
    const [granted, setGranted] = useState(false);
    const [searchCap, setSearchCap] = useState("");
    const [searchPostType, setSearchPostType] = useState("");
    const {
        userRoleCapabilities,
        selectedRole,
        column,
        setColumn,
        roleCapabilities,
        processedDefaultCapabilities,
        setRoleCapabilities,
        postTypes,
        handleActiveGroup,
        activeGroup,
        selectAll,
        setSelectAll,
        denyAll,
        setDenyAll,
    } = useGlobalContext();

    const searchCapabilityRef = useRef(null);

    // sorting table format's capabilities order
    const sortedTableCaps = (post_type) => {
        const tableCapabilitiesGroup = roleCapabilities
            .filter((cap) => {
                return cap.group == activeGroup;
            })
            .filter((cap) => {
                return cap.cap_id.match(post_type);
            });

        if (activeGroup === "editing" && (post_type === "posts" || post_type === "pages")) {
            let swap_2_3 = tableCapabilitiesGroup.splice(2, 1);
            tableCapabilitiesGroup.splice(3, 0, swap_2_3[0]);

            let swap_5_1 = tableCapabilitiesGroup.splice(5, 1);
            tableCapabilitiesGroup.splice(1, 0, swap_5_1[0]);
        }
        return tableCapabilitiesGroup;
    };

    const handleSearch = (e) => {
        setSearchCap(e.target.value);
    };

    const handleSearchClear = (e) => {
        searchCapabilityRef.current.value = "";
        setSearchCap("");
    };

    const handleSearchPostType = (post_type) => {
        setSearchPostType(post_type);
    };

    const handleSearchPostTypeClear = (e) => {
        setSearchPostType("");
    };

    const post_types = postTypes.map((post) => {
        let label = post.charAt(0).toUpperCase() + post.slice(1).toLowerCase();
        return { value: post, label: label };
    });

    const columnHandler = (col) => {
        setColumn(col.value);
    };

    const grantedOnly = () => {
        setGranted((old_state) => {
            return !old_state;
        });
    };

    const handleCapabilitySelection = (cap_id, checked, selectedRole) => {
        if (selectedRole.value === "administrator") return;
        let newCapabilities = roleCapabilities.map((item) => {
            if (item.cap_id == cap_id) {
                item.status = checked;
            }
            return item;
        });
        setRoleCapabilities(newCapabilities);
    };

    const selectAllHandler = (value) => {
        setDenyAll(false);
        setSelectAll((old_state) => {
            return true;
        });
    };

    const denyAllHandler = (value) => {
        setSelectAll(false);
        if (selectedRole.value !== "administrator") {
            setDenyAll((old_state) => {
                return true;
            });
        }
    };

    const handleSelectedColumn = (col) => {
        let label = `Column ${col}`;
        return { value: col, label: label };
    };

    const selectDeselect = (roleCapabilities, selectAll, denyAll, selectedRole) => {
        let newCapabilities = roleCapabilities.map((item) => {
            if (item.group == activeGroup) {
                if (selectAll === true) {
                    item.status = true;
                }
                if (selectedRole.value !== "administrator" && denyAll === true) {
                    item.status = false;
                }
            }
            return item;
        });
        setRoleCapabilities(newCapabilities);
    };

    useEffect(() => {
        let capabilities = processedDefaultCapabilities(
            userRoleCapabilities,
            selectedRole.value,
            postTypes
        );
        setRoleCapabilities(capabilities);
        setDenyAll(false);
        setSelectAll(false);
    }, [selectedRole]);

    useEffect(() => {
        selectDeselect(roleCapabilities, selectAll, denyAll, selectedRole);
    }, [selectAll, denyAll]);

    return (
        <>
            <div className="adminify-user-role-editor-body is-pulled-right mt-6">
                <div className="top-heading">
                    <div className="actions is-flex is-pulled-right">
                        <label className="mr-3" htmlFor="granted-only">
                            <input id="granted-only" type="checkbox" onChange={grantedOnly} />
                            Granted Only
                        </label>
                        {activeGroup !== "editing" &&
                            activeGroup !== "deletion" &&
                            activeGroup !== "reading" && (
                                <Select
                                    fontSize="16px"
                                    className="action-select-container"
                                    classNamePrefix="action-select"
                                    options={options}
                                    defaultValue={handleSelectedColumn(column) || options[2]}
                                    onChange={columnHandler}
                                    placeholder="Select Action"
                                    styles={colourStyles}
                                />
                            )}
                        <SaveButton />
                    </div>
                </div>
                <div className="role-capabilities-settings is-flex">
                    <div className="role-list">
                        <ul>
                            {Object.keys(roleGroups).map((item, k) => {
                                let list;
                                let label = roleGroups[item].label;
                                list = (
                                    <li
                                        key={k}
                                        className={
                                            activeGroup == label.toLowerCase() ? "active" : ""
                                        }
                                        onClick={handleActiveGroup}>
                                        {label}
                                    </li>
                                );
                                return list;
                            })}
                        </ul>
                    </div>
                    <div className="capabilities-list">
                        <div className="capabilitiy-heading">
                            <h5>Post Capabilities</h5>
                            <div className="capabilitiy-options is-flex is-pulled-right">
                                <div className="capabilitiy-option mr-5">
                                    <label htmlFor="select-all">
                                        <input
                                            name="select-all"
                                            id="select-all"
                                            type="checkbox"
                                            onChange={(e) => selectAllHandler(e.target.checked)}
                                            checked={selectAll}
                                        />{" "}
                                        Select All
                                    </label>
                                </div>
                                <div className="capabilitiy-option option-deny mr-5">
                                    <label htmlFor="deny-all">
                                        <input
                                            name="deny-all"
                                            id="deny-all"
                                            type="checkbox"
                                            onChange={(e) => denyAllHandler(e.target.checked)}
                                            checked={denyAll}
                                        />{" "}
                                        Deny All
                                    </label>
                                </div>
                                {activeGroup !== "editing" &&
                                activeGroup !== "deletion" &&
                                activeGroup !== "reading" ? (
                                    <div className="capabilitiy-option">
                                        <input
                                            ref={searchCapabilityRef}
                                            type="text"
                                            placeholder="Filter by capability"
                                            onChange={handleSearch}
                                        />
                                        <button
                                            className="page-title-action user-role-editor-action-button"
                                            onClick={handleSearchClear}>
                                            Clear
                                        </button>
                                    </div>
                                ) : (
                                    <div className="capabilitiy-option">
                                        <Select
                                            fontSize="16px"
                                            className="post-select-container"
                                            classNamePrefix="post-select"
                                            options={post_types}
                                            value={searchPostType || ""}
                                            onChange={handleSearchPostType}
                                            placeholder="Filter by post type"
                                            styles={colourStyles}
                                        />
                                        <button
                                            className="page-title-action user-role-editor-action-button"
                                            onClick={handleSearchPostTypeClear}>
                                            Clear
                                        </button>
                                    </div>
                                )}
                            </div>
                        </div>
                        <div className="capabilities-body">
                            {activeGroup !== "editing" &&
                            activeGroup !== "deletion" &&
                            activeGroup !== "reading" ? (
                                <div
                                    className="capability-items mt-5"
                                    style={{ columnCount: column }}>
                                    {roleCapabilities
                                        .filter((item) => {
                                            return item.group == activeGroup;
                                        })
                                        .filter((item) => {
                                            return item.cap_id.includes(searchCap);
                                        })
                                        .map((item, i) => {
                                            if (granted && item.status !== granted) return;
                                            return (
                                                <div key={i} className="capability-item">
                                                    <label htmlFor={item.cap_id}>
                                                        <input
                                                            type="checkbox"
                                                            id={item.cap_id}
                                                            checked={item.status}
                                                            onChange={(e) =>
                                                                handleCapabilitySelection(
                                                                    item.cap_id,
                                                                    e.target.checked,
                                                                    selectedRole
                                                                )
                                                            }
                                                        />{" "}
                                                        {item.cap_id}
                                                    </label>
                                                </div>
                                            );
                                        })}
                                </div>
                            ) : (
                                <table className="mt-5">
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            {activeGroup === "editing" && (
                                                <>
                                                    <td>Edit</td>
                                                    <td>Create</td>
                                                    <td>Edit Others</td>
                                                    <td>Publish</td>
                                                    <td>Edit Published</td>
                                                    <td>Edit Private</td>
                                                </>
                                            )}
                                            {activeGroup === "deletion" && (
                                                <>
                                                    <td>Delete</td>
                                                    <td>Delete Others</td>
                                                    <td>Delete Published</td>
                                                    <td>Delete Private</td>
                                                </>
                                            )}
                                            {activeGroup === "reading" && (
                                                <>
                                                    <td>Reading Private</td>
                                                </>
                                            )}
                                        </tr>
                                        {postTypes
                                            .filter((item) => {
                                                let newItem = item;
                                                if (searchPostType !== "") {
                                                    newItem = item == searchPostType.value;
                                                }
                                                return newItem;
                                            })
                                            .map((item, i) => {
                                                return (
                                                    <tr key={i}>
                                                        <td className="post-type">{item}</td>
                                                        {sortedTableCaps(item).map((cap, j) => {
                                                            return (
                                                                <td key={j}>
                                                                    <input
                                                                        type="checkbox"
                                                                        id={cap.cap_id}
                                                                        checked={cap.status}
                                                                        onChange={(e) => {
                                                                            handleCapabilitySelection(
                                                                                cap.cap_id,
                                                                                e.target.checked,
                                                                                selectedRole
                                                                            );
                                                                            if (e.target.checked) {
                                                                                setDenyAll(false);
                                                                            } else {
                                                                                setSelectAll(false);
                                                                            }
                                                                        }}
                                                                    />
                                                                    {/* {cap.cap_id} */}
                                                                </td>
                                                            );
                                                        })}
                                                    </tr>
                                                );
                                            })}
                                    </tbody>
                                </table>
                            )}
                        </div>

                        <SaveButton />
                    </div>
                </div>
            </div>
        </>
    );
};

export default Body;
