from ortools.linear_solver import pywraplp
import openpyxl as op

def binpacking():
    
    solver = pywraplp.Solver('binpacking', pywraplp.Solver.CBC_MIXED_INTEGER_PROGRAMMING)


    
    poids = [5, 4, 3, 2, 9, 6, 8, 1, 4, 7]
    
    objets = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
    
    vol = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
    
    nombres = [15,52,39,18,45,27,12,12,14,31]
    
    capacite = 100

    X = []
    
    objective = solver.Objective()
    Y = []
    for i in range(10):
        U = []
        y = solver.IntVar(0, 1, 'y_'+str(i))
        Y.append(y)
        ct_nombre = solver.Constraint(0, nombres[i], 'nombre' + str(i))
        ct_nombre = solver.Constraint(nombres[i],0, 'nombre' + str(i))
        ct_capacite = solver.Constraint(0, capacite, 'ct_capacite_y' + str(i))
        objective.SetCoefficient(Y[i], 1)
        for j in range(10):
            x = solver.IntVar(0, 1, 'x_' + str(i) + '_' + str(j))
            U.append(x)
            ct_capacite.SetCoefficient(x, poids[i])
            ct_nombre.SetCoefficient(x, 1)
        X.append(U)
    
    
    objective.SetMinimization() 

    solver.Solve()
    
    print('Solution:')
    print('Valeur optimale =', solver.Objective().Value())
    for i in range(10):
        print(f"y_{i}: {Y[i].solution_value()}")
    


binpacking()
