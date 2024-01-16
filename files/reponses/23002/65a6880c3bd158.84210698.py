from ortools.linear_solver import pywraplp

def binpacking():
    solver = pywraplp.Solver('binpacking', pywraplp.Solver.CBC_MIXED_INTEGER_PROGRAMMING)

    poids = [5, 4, 3, 2, 9, 6, 8, 1, 4, 7]
    objets = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
    vol = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
    nombres = [15, 52, 39, 18, 45, 27, 12, 12, 14, 31]
    capacite = 100
    
    X = []
    for i in objets:
        U = []
        for j in vol:
            x = solver.IntVar(0, 1, 'x_' + str(i) + '_' + str(j))
            U.append(x)
        X.append(U)
        
    Y = []
    for i in vol:
        y = solver.IntVar(0, 1, 'y_'+str(i))
        Y.append(y)
        
    objective = solver.Objective()
    for i in range(len(vol)):
        objective.SetCoefficient(Y[i], 1)
            
    objective.SetMinimization()    

    # Contraintes de capacité des vols
    for j in range(len(vol)):
        ct_capacite = solver.Constraint(0, capacite, 'ct_capacite_' + str(j))
        for i in range(len(objets)):
            ct_capacite.SetCoefficient(X[i][j], poids[i])

    # Contraintes d'assignation des objets
    for i in range(len(objets)):
        ct_assignation = solver.Constraint(1, 1, 'ct_assignation_' + str(i))
        for j in range(len(vol)):
            ct_assignation.SetCoefficient(X[i][j], 1)

    solver.Solve()
    print('Solution:')
    print('Valeur optimale =', solver.Objective().Value())
    
    for i in range(len(objets)):  
        for j in range(len(vol)):
            print(X[i][j].solution_value())

binpacking()
