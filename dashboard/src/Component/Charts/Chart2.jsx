import React from 'react'
import { Cell, Pie, PieChart } from 'recharts'

const Chart2 = () => {

    const data = [
        {
            "name": "Group A",
            "value": 400
        },
        {
            "name": "Group B",
            "value": 300
        },
        {
            "name": "Group C",
            "value": 500
        },
        {
            "name": "Group D",
            "value": 200
        },
        {
            "name": "Group E",
            "value": 278
        },
        {
            "name": "Group F",
            "value": 189
        }
    ]

        const colors = ['#8884d8', '#82ca9d', '#ffc658', '#ff8042', '#00C49F', '#FFBB28']

    return (
        <div>
            <PieChart width={170} height={250}>
                <Pie data={data} cx="50%" cy="50%" outerRadius={80} label>
                    {
                        data.map((entry, index) => (
                            <Cell key={`cell-${index}`} fill={colors[index]} />
                        ))
                    }
                </Pie>
            </PieChart>

        </div>
    )
}

export default Chart2
