import { Grid, MuiThemeProvider, Button } from "@material-ui/core";
import { createMuiTheme } from "@material-ui/core/styles";
import React, { Component } from "react";
import MaterialTable from 'material-table';
import Typography from "@material-ui/core/Typography";

let direction = "ltr";
// direction = 'rtl';
const theme = createMuiTheme({
    direction: direction,
    palette: {
        type: "light",
    },
});

const bigData = [];
for (let i = 0; i < 1; i++) {
    const d = {
        id: i + 1,
        name: "Name" + i,
        surname: "Surname" + Math.round(i / 10),
        isMarried: i % 2 ? true : false,
        birthDate: new Date(1987, 1, 1),
        birthCity: 0,
        sex: i % 2 ? "Male" : "Female",
        type: "adult",
        insertDateTime: new Date(2018, 1, 1, 12, 23, 44),
        time: new Date(1900, 1, 1, 14, 23, 35),
    };
    bigData.push(d);
}

function ExampleTable() {
    return (
        <MaterialTable
            title="Basic Tree Data Preview"
            data={[
                {
                    id: 7,
                    name: 'g',
                    surname: 'Baran',
                    birthYear: 1987,
                    birthCity: 34,
                    sex: 'Female',
                    type: 'child',
                    parentId: 3,
                },
                {
                    id: 5,
                    name: 'e',
                    surname: 'Baran',
                    birthYear: 1987,
                    birthCity: 34,
                    sex: 'Female',
                    type: 'child',
                },
                {
                    id: 1,
                    name: 'a',
                    surname: 'Baran',
                    birthYear: 1987,
                    birthCity: 63,
                    sex: 'Male',
                    type: 'adult',
                },
                {
                    id: 2,
                    name: 'b',
                    surname: 'Baran',
                    birthYear: 1987,
                    birthCity: 34,
                    sex: 'Female',
                    type: 'adult',
                    parentId: 1,
                },
                {
                    id: 3,
                    name: 'c',
                    surname: 'Baran',
                    birthYear: 1987,
                    birthCity: 34,
                    sex: 'Female',
                    type: 'child',
                    parentId: 1,
                },
                {
                    id: 4,
                    name: 'omer',
                    surname: 'Baran',
                    birthYear: 1987,
                    birthCity: 34,
                    sex: 'Female',
                    type: 'child',
                    parentId: 3,
                },
                {
                    id: 6,
                    name: 'f',
                    surname: 'Baran',
                    birthYear: 1987,
                    birthCity: 34,
                    sex: 'Female',
                    type: 'child',
                    parentId: 5,
                },
            ]}
            columns={[
                { title: 'Adı', field: 'name' },
                { title: 'Soyadı', field: 'surname' },
                { title: 'Cinsiyet', field: 'sex' },
                { title: 'Tipi', field: 'type', removable: false },
                { title: 'Doğum Yılı', field: 'birthYear', type: 'numeric' },
                {
                    title: 'Doğum Yeri',
                    field: 'birthCity',
                    lookup: { 34: 'İstanbul', 63: 'Şanlıurfa' },
                },
            ]}
            parentChildData={(row, rows) => rows.find(a => a.id === row.parentId)}
            options={{
                // selection: true,
                // searchAutoFocus: true,
                search:true
            }}
        />
    )
}


export default ExampleTable;
