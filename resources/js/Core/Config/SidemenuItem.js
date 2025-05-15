export const navItems = [
    {
        "label": "Dashboard",
        "href": "/dashboard",
        "icon": "home"
    },
    {
        "label": "Data Management",
        "type": "header",
        "permission": [
            "user.browse",
            "role.browse",
            "user_log.browse",
        ]
    },
    {
        "label": "Master Data",
        "href": "/master",
        "icon": "inbox-stack",
        "permission": [],
    },
    {
        "label": "User Management",
        "href": "/users",
        "icon": "users",
        "permission": [
            "user.browse",
            "role.browse",
            "user_log.browse",
        ],
        "submenu": [
            {
                "label": "User",
                "href": "/user",
                "permission": "user.browse"
            },
            {
                "label": "Role & Permission",
                "href": "/role-and-permission",
                "permission": "role.browse"
            },
            {
                "label": "User Log",
                "href": "/user-log",
                "permission": "user_log.browse"
            }
        ]
    },
    {
        "label": "Master Data",
        "href": "/master-data",
        "icon": "archive-box",
        "permission": [
            "warehouse.browse",
            "cctv.browse"
        ],
        "submenu": [
            {
                "label": "Warehouse",
                "href": "/warehouse",
                "permission": "warehouse.browse"
            },
            {
                "label": "CCTV",
                "href": "/cctv",
                "permission": "cctv.browse"
            },
            {
                "label": "Bag",
                "href": "/bag",
                "permission": "bag.browse"
            },
            {
                "label": "Shift",
                "href": "/shift",
                "permission": "shift.browse"
            }
        ]
    }  
];