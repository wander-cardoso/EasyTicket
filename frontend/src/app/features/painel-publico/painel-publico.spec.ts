import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PainelPublico } from './painel-publico';

describe('PainelPublico', () => {
  let component: PainelPublico;
  let fixture: ComponentFixture<PainelPublico>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PainelPublico],
    }).compileComponents();

    fixture = TestBed.createComponent(PainelPublico);
    component = fixture.componentInstance;
    await fixture.whenStable();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
